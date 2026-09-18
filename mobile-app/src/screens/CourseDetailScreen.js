/**
 * Course Detail Screen
 * Display individual course with lessons
 */

import React, { useState, useEffect } from 'react';
import {
  View,
  ScrollView,
  TouchableOpacity,
  Text,
  StyleSheet,
  ActivityIndicator,
  Image,
  Alert,
  FlatList,
} from 'react-native';
import APIService from '../services/api';
import StorageService from '../services/storage';

export default function CourseDetailScreen({ route, navigation }) {
  const { courseId } = route.params;
  const [course, setCourse] = useState(null);
  const [lessons, setLessons] = useState([]);
  const [loading, setLoading] = useState(true);
  const [enrolling, setEnrolling] = useState(false);
  const [enrolled, setEnrolled] = useState(false);
  const [error, setError] = useState(null);

  useEffect(() => {
    fetchCourseDetail();
  }, [courseId]);

  const fetchCourseDetail = async () => {
    try {
      setLoading(true);
      setError(null);

      const courseResponse = await APIService.getCourseById(courseId);
      const courseData = courseResponse.data || courseResponse.course;
      setCourse(courseData);
      
      // Check if user is enrolled
      setEnrolled(courseData.is_enrolled || courseData.enrolled);

      // Fetch lessons
      const lessonsResponse = await APIService.getCourseLessons(courseId);
      const lessonsData = lessonsResponse.data || lessonsResponse.lessons || [];
      setLessons(lessonsData);
      await StorageService.cacheLessons(courseId, lessonsData);
    } catch (err) {
      setError('Failed to load course details. Please try again.');
      console.error('Error fetching course:', err);
    } finally {
      setLoading(false);
    }
  };

  const handleEnroll = async () => {
    try {
      setEnrolling(true);
      await APIService.enrollCourse(courseId);
      setEnrolled(true);
      Alert.alert('Success', 'You have successfully enrolled in this course');
    } catch (err) {
      Alert.alert('Enrollment Failed', err.data?.message || 'Unable to enroll');
    } finally {
      setEnrolling(false);
    }
  };

  const renderLessonItem = ({ item, index }) => (
    <TouchableOpacity
      style={styles.lessonItem}
      onPress={() =>
        enrolled
          ? navigation.navigate('LessonDetail', { lessonId: item.id })
          : Alert.alert('Enrollment Required', 'Please enroll in this course first')
      }
      disabled={!enrolled}
    >
      <View style={styles.lessonNumber}>
        <Text style={styles.lessonNumberText}>{index + 1}</Text>
      </View>
      <View style={styles.lessonInfo}>
        <Text style={styles.lessonTitle}>{item.title}</Text>
        <Text style={styles.lessonDuration}>{item.duration || 'N/A'} mins</Text>
      </View>
      {enrolled && <Text style={styles.lessonArrow}>→</Text>}
      {!enrolled && <Text style={styles.lockIcon}>🔒</Text>}
    </TouchableOpacity>
  );

  if (loading) {
    return (
      <View style={styles.centerContainer}>
        <ActivityIndicator size="large" color="#007AFF" />
      </View>
    );
  }

  if (!course) {
    return (
      <View style={styles.centerContainer}>
        <Text style={styles.errorText}>Course not found</Text>
        <TouchableOpacity
          style={styles.backButton}
          onPress={() => navigation.goBack()}
        >
          <Text style={styles.backButtonText}>Go Back</Text>
        </TouchableOpacity>
      </View>
    );
  }

  return (
    <ScrollView style={styles.container}>
      {/* Course Header Image */}
      {course.image && (
        <Image
          source={{ uri: course.image }}
          style={styles.courseImage}
        />
      )}

      {/* Course Info */}
      <View style={styles.courseHeader}>
        <Text style={styles.courseTitle}>{course.title}</Text>
        {course.instructor && (
          <Text style={styles.instructor}>By {course.instructor.name}</Text>
        )}
        <View style={styles.ratingContainer}>
          <Text style={styles.rating}>⭐ {course.rating || 'N/A'}</Text>
          <Text style={styles.students}>👥 {course.students_count || 0} students</Text>
        </View>
      </View>

      {/* Description */}
      <View style={styles.section}>
        <Text style={styles.sectionTitle}>About This Course</Text>
        <Text style={styles.description}>{course.full_description || course.description}</Text>
      </View>

      {/* Course Stats */}
      <View style={styles.statsContainer}>
        <View style={styles.stat}>
          <Text style={styles.statValue}>{lessons.length}</Text>
          <Text style={styles.statLabel}>Lessons</Text>
        </View>
        <View style={styles.stat}>
          <Text style={styles.statValue}>
            {course.total_duration || (lessons.length * 30)} mins
          </Text>
          <Text style={styles.statLabel}>Duration</Text>
        </View>
        <View style={styles.stat}>
          <Text style={styles.statValue}>{course.level || 'Mixed'}</Text>
          <Text style={styles.statLabel}>Level</Text>
        </View>
      </View>

      {/* Enroll Button */}
      {!enrolled && (
        <TouchableOpacity
          style={[styles.enrollButton, enrolling && styles.disabledButton]}
          onPress={handleEnroll}
          disabled={enrolling}
        >
          {enrolling ? (
            <ActivityIndicator color="#fff" />
          ) : (
            <Text style={styles.enrollButtonText}>
              Enroll Now {course.price ? `- $${course.price}` : '(Free)'}
            </Text>
          )}
        </TouchableOpacity>
      )}

      {/* Lessons Section */}
      <View style={[styles.section, styles.lessonsSection]}>
        <Text style={styles.sectionTitle}>Course Content ({lessons.length} lessons)</Text>
        {lessons.length > 0 ? (
          <FlatList
            data={lessons}
            renderItem={renderLessonItem}
            keyExtractor={item => item.id.toString()}
            scrollEnabled={false}
            contentContainerStyle={styles.lessonsList}
          />
        ) : (
          <Text style={styles.emptyText}>No lessons available yet</Text>
        )}
      </View>

      {error && (
        <View style={styles.errorContainer}>
          <Text style={styles.errorText}>{error}</Text>
          <TouchableOpacity
            style={styles.retryButton}
            onPress={fetchCourseDetail}
          >
            <Text style={styles.retryButtonText}>Retry</Text>
          </TouchableOpacity>
        </View>
      )}
    </ScrollView>
  );
}

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: '#f5f5f5',
  },
  centerContainer: {
    flex: 1,
    justifyContent: 'center',
    alignItems: 'center',
    backgroundColor: '#f5f5f5',
  },
  courseImage: {
    width: '100%',
    height: 220,
    backgroundColor: '#e0e0e0',
  },
  courseHeader: {
    backgroundColor: '#fff',
    paddingHorizontal: 16,
    paddingVertical: 16,
    borderBottomWidth: 1,
    borderBottomColor: '#e0e0e0',
  },
  courseTitle: {
    fontSize: 22,
    fontWeight: 'bold',
    color: '#333',
    marginBottom: 8,
  },
  instructor: {
    fontSize: 14,
    color: '#666',
    marginBottom: 8,
  },
  ratingContainer: {
    flexDirection: 'row',
    gap: 16,
  },
  rating: {
    fontSize: 14,
    color: '#f39c12',
    fontWeight: '600',
  },
  students: {
    fontSize: 14,
    color: '#666',
  },
  section: {
    backgroundColor: '#fff',
    marginHorizontal: 12,
    marginVertical: 8,
    paddingHorizontal: 16,
    paddingVertical: 12,
    borderRadius: 8,
  },
  lessonsSection: {
    marginBottom: 24,
  },
  sectionTitle: {
    fontSize: 16,
    fontWeight: 'bold',
    color: '#333',
    marginBottom: 12,
  },
  description: {
    fontSize: 14,
    color: '#666',
    lineHeight: 20,
  },
  statsContainer: {
    flexDirection: 'row',
    justifyContent: 'space-around',
    backgroundColor: '#fff',
    marginHorizontal: 12,
    marginVertical: 8,
    paddingVertical: 16,
    borderRadius: 8,
  },
  stat: {
    alignItems: 'center',
  },
  statValue: {
    fontSize: 18,
    fontWeight: 'bold',
    color: '#007AFF',
    marginBottom: 4,
  },
  statLabel: {
    fontSize: 12,
    color: '#666',
  },
  enrollButton: {
    marginHorizontal: 12,
    marginVertical: 16,
    backgroundColor: '#007AFF',
    paddingVertical: 14,
    borderRadius: 8,
    alignItems: 'center',
  },
  disabledButton: {
    opacity: 0.6,
  },
  enrollButtonText: {
    color: '#fff',
    fontSize: 16,
    fontWeight: 'bold',
  },
  lessonsList: {
    gap: 8,
  },
  lessonItem: {
    flexDirection: 'row',
    alignItems: 'center',
    paddingVertical: 12,
    paddingHorizontal: 12,
    backgroundColor: '#f9f9f9',
    borderRadius: 6,
    borderLeftWidth: 4,
    borderLeftColor: '#007AFF',
  },
  lessonNumber: {
    width: 32,
    height: 32,
    borderRadius: 16,
    backgroundColor: '#007AFF',
    justifyContent: 'center',
    alignItems: 'center',
    marginRight: 12,
  },
  lessonNumberText: {
    color: '#fff',
    fontWeight: 'bold',
    fontSize: 14,
  },
  lessonInfo: {
    flex: 1,
  },
  lessonTitle: {
    fontSize: 14,
    fontWeight: '600',
    color: '#333',
    marginBottom: 4,
  },
  lessonDuration: {
    fontSize: 12,
    color: '#999',
  },
  lessonArrow: {
    fontSize: 18,
    color: '#999',
  },
  lockIcon: {
    fontSize: 18,
  },
  emptyText: {
    fontSize: 14,
    color: '#999',
    textAlign: 'center',
    paddingVertical: 20,
  },
  errorContainer: {
    backgroundColor: '#fee',
    marginHorizontal: 12,
    marginVertical: 12,
    paddingHorizontal: 16,
    paddingVertical: 12,
    borderRadius: 8,
    borderLeftWidth: 4,
    borderLeftColor: '#dc3545',
  },
  errorText: {
    color: '#721c24',
    fontSize: 14,
    marginBottom: 8,
  },
  retryButton: {
    backgroundColor: '#dc3545',
    paddingHorizontal: 16,
    paddingVertical: 8,
    borderRadius: 4,
    alignSelf: 'flex-start',
  },
  retryButtonText: {
    color: '#fff',
    fontSize: 12,
    fontWeight: '600',
  },
  backButton: {
    marginTop: 16,
    paddingHorizontal: 24,
    paddingVertical: 10,
    backgroundColor: '#007AFF',
    borderRadius: 6,
  },
  backButtonText: {
    color: '#fff',
    fontSize: 14,
    fontWeight: '600',
  },
});
