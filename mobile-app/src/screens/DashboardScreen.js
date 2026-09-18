/**
 * Dashboard/Home Screen
 * Main landing page with overview and quick actions
 */

import React, { useState, useEffect } from 'react';
import {
  View,
  ScrollView,
  TouchableOpacity,
  Text,
  StyleSheet,
  ActivityIndicator,
  RefreshControl,
} from 'react-native';
import { useAuth } from '../context/AuthContext';
import APIService from '../services/api';

export default function DashboardScreen({ navigation }) {
  const { state } = useAuth();
  const [dashboard, setDashboard] = useState(null);
  const [loading, setLoading] = useState(true);
  const [refreshing, setRefreshing] = useState(false);

  useEffect(() => {
    fetchDashboard();
  }, []);

  const fetchDashboard = async (isRefresh = false) => {
    try {
      if (isRefresh) setRefreshing(true);
      else setLoading(true);

      const response = await APIService.getDashboard();
      setDashboard(response.data || response);
    } catch (err) {
      console.error('Error fetching dashboard:', err);
    } finally {
      setLoading(false);
      setRefreshing(false);
    }
  };

  if (loading) {
    return (
      <View style={styles.centerContainer}>
        <ActivityIndicator size="large" color="#007AFF" />
      </View>
    );
  }

  return (
    <ScrollView
      style={styles.container}
      refreshControl={
        <RefreshControl
          refreshing={refreshing}
          onRefresh={() => fetchDashboard(true)}
          colors={['#007AFF']}
        />
      }
    >
      {/* Welcome Header */}
      <View style={styles.header}>
        <Text style={styles.greeting}>Welcome back, {state.user?.name}! 👋</Text>
        <Text style={styles.date}>
          {new Date().toLocaleDateString('en-US', {
            weekday: 'long',
            year: 'numeric',
            month: 'long',
            day: 'numeric',
          })}
        </Text>
      </View>

      {/* Quick Stats */}
      <View style={styles.statsContainer}>
        <View style={styles.stat}>
          <Text style={styles.statNumber}>{dashboard?.enrolled_courses || 0}</Text>
          <Text style={styles.statLabel}>Enrolled</Text>
        </View>
        <View style={styles.stat}>
          <Text style={styles.statNumber}>{dashboard?.completed_lessons || 0}</Text>
          <Text style={styles.statLabel}>Completed</Text>
        </View>
        <View style={styles.stat}>
          <Text style={styles.statNumber}>{dashboard?.in_progress_courses || 0}</Text>
          <Text style={styles.statLabel}>In Progress</Text>
        </View>
        <View style={styles.stat}>
          <Text style={styles.statNumber}>{dashboard?.total_hours || 0}h</Text>
          <Text style={styles.statLabel}>Learning Hours</Text>
        </View>
      </View>

      {/* Quick Actions */}
      <View style={styles.section}>
        <Text style={styles.sectionTitle}>Quick Actions</Text>
        <View style={styles.actionsGrid}>
          <TouchableOpacity
            style={styles.actionButton}
            onPress={() => navigation.navigate('Courses')}
          >
            <Text style={styles.actionIcon}>📚</Text>
            <Text style={styles.actionText}>Browse Courses</Text>
          </TouchableOpacity>

          <TouchableOpacity
            style={styles.actionButton}
            onPress={() => navigation.navigate('ZoomSessions')}
          >
            <Text style={styles.actionIcon}>📹</Text>
            <Text style={styles.actionText}>Live Sessions</Text>
          </TouchableOpacity>

          <TouchableOpacity
            style={styles.actionButton}
            onPress={() => navigation.navigate('Subscriptions')}
          >
            <Text style={styles.actionIcon}>💳</Text>
            <Text style={styles.actionText}>Subscription</Text>
          </TouchableOpacity>

          <TouchableOpacity
            style={styles.actionButton}
            onPress={() => navigation.navigate('Messages')}
          >
            <Text style={styles.actionIcon}>💬</Text>
            <Text style={styles.actionText}>Messages</Text>
          </TouchableOpacity>
        </View>
      </View>

      {/* In Progress Courses */}
      {dashboard?.in_progress && dashboard.in_progress.length > 0 && (
        <View style={styles.section}>
          <View style={styles.sectionHeader}>
            <Text style={styles.sectionTitle}>Continue Learning</Text>
            <TouchableOpacity onPress={() => navigation.navigate('Courses')}>
              <Text style={styles.seeAll}>See All →</Text>
            </TouchableOpacity>
          </View>

          {dashboard.in_progress.map((course, index) => (
            <TouchableOpacity
              key={index}
              style={styles.courseItem}
              onPress={() =>
                navigation.navigate('CourseDetail', { courseId: course.id })
              }
            >
              <View style={styles.courseProgress}>
                <Text style={styles.courseTitle}>{course.title}</Text>
                <View style={styles.progressBar}>
                  <View
                    style={[
                      styles.progressFill,
                      { width: `${course.progress || 0}%` },
                    ]}
                  />
                </View>
                <Text style={styles.progressText}>{course.progress || 0}% complete</Text>
              </View>
            </TouchableOpacity>
          ))}
        </View>
      )}

      {/* Upcoming Sessions */}
      {dashboard?.upcoming_sessions && dashboard.upcoming_sessions.length > 0 && (
        <View style={styles.section}>
          <Text style={styles.sectionTitle}>Upcoming Live Sessions</Text>
          {dashboard.upcoming_sessions.map((session, index) => (
            <View key={index} style={styles.sessionItem}>
              <View style={styles.sessionTime}>
                <Text style={styles.sessionDate}>
                  {new Date(session.scheduled_at).toLocaleDateString()}
                </Text>
                <Text style={styles.sessionClock}>⏰</Text>
              </View>
              <View style={styles.sessionDetails}>
                <Text style={styles.sessionTitle}>{session.title}</Text>
                <Text style={styles.sessionInstructor}>
                  with {session.instructor_name}
                </Text>
              </View>
              <TouchableOpacity style={styles.joinButton}>
                <Text style={styles.joinButtonText}>Join →</Text>
              </TouchableOpacity>
            </View>
          ))}
        </View>
      )}

      {/* Recommendations */}
      {dashboard?.recommended && dashboard.recommended.length > 0 && (
        <View style={[styles.section, { marginBottom: 32 }]}>
          <Text style={styles.sectionTitle}>Recommended For You</Text>
          {dashboard.recommended.slice(0, 3).map((course, index) => (
            <TouchableOpacity
              key={index}
              style={styles.recommendedItem}
              onPress={() =>
                navigation.navigate('CourseDetail', { courseId: course.id })
              }
            >
              <Text style={styles.recommendedIcon}>⭐</Text>
              <View style={styles.recommendedInfo}>
                <Text style={styles.recommendedTitle}>{course.title}</Text>
                <Text style={styles.recommendedCategory}>{course.category}</Text>
              </View>
              <Text style={styles.recommendedArrow}>→</Text>
            </TouchableOpacity>
          ))}
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
  header: {
    backgroundColor: '#007AFF',
    paddingHorizontal: 16,
    paddingVertical: 20,
    paddingTop: 32,
  },
  greeting: {
    fontSize: 24,
    fontWeight: 'bold',
    color: '#fff',
    marginBottom: 4,
  },
  date: {
    fontSize: 14,
    color: '#e3f2fd',
  },
  statsContainer: {
    flexDirection: 'row',
    flexWrap: 'wrap',
    paddingHorizontal: 8,
    marginVertical: 12,
  },
  stat: {
    width: '25%',
    alignItems: 'center',
    paddingVertical: 12,
  },
  statNumber: {
    fontSize: 24,
    fontWeight: 'bold',
    color: '#333',
    marginBottom: 4,
  },
  statLabel: {
    fontSize: 12,
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
  sectionHeader: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    marginBottom: 12,
  },
  sectionTitle: {
    fontSize: 16,
    fontWeight: 'bold',
    color: '#333',
  },
  seeAll: {
    fontSize: 13,
    color: '#007AFF',
    fontWeight: '600',
  },
  actionsGrid: {
    flexDirection: 'row',
    flexWrap: 'wrap',
    justifyContent: 'space-between',
  },
  actionButton: {
    width: '48%',
    alignItems: 'center',
    paddingVertical: 16,
    backgroundColor: '#f9f9f9',
    borderRadius: 8,
    marginBottom: 12,
  },
  actionIcon: {
    fontSize: 32,
    marginBottom: 8,
  },
  actionText: {
    fontSize: 12,
    color: '#333',
    fontWeight: '600',
    textAlign: 'center',
  },
  courseItem: {
    paddingVertical: 12,
    borderBottomWidth: 1,
    borderBottomColor: '#e0e0e0',
  },
  courseProgress: {
    gap: 8,
  },
  courseTitle: {
    fontSize: 14,
    fontWeight: '600',
    color: '#333',
  },
  progressBar: {
    height: 6,
    backgroundColor: '#e0e0e0',
    borderRadius: 3,
    overflow: 'hidden',
  },
  progressFill: {
    height: '100%',
    backgroundColor: '#007AFF',
  },
  progressText: {
    fontSize: 12,
    color: '#666',
  },
  sessionItem: {
    flexDirection: 'row',
    alignItems: 'center',
    paddingVertical: 12,
    borderBottomWidth: 1,
    borderBottomColor: '#e0e0e0',
  },
  sessionTime: {
    width: 60,
    alignItems: 'center',
  },
  sessionDate: {
    fontSize: 12,
    color: '#666',
  },
  sessionClock: {
    fontSize: 20,
    marginTop: 4,
  },
  sessionDetails: {
    flex: 1,
    marginLeft: 12,
  },
  sessionTitle: {
    fontSize: 14,
    fontWeight: '600',
    color: '#333',
    marginBottom: 2,
  },
  sessionInstructor: {
    fontSize: 12,
    color: '#999',
  },
  joinButton: {
    paddingHorizontal: 12,
    paddingVertical: 6,
    backgroundColor: '#007AFF',
    borderRadius: 4,
  },
  joinButtonText: {
    color: '#fff',
    fontSize: 12,
    fontWeight: '600',
  },
  recommendedItem: {
    flexDirection: 'row',
    alignItems: 'center',
    paddingVertical: 12,
    borderBottomWidth: 1,
    borderBottomColor: '#e0e0e0',
  },
  recommendedIcon: {
    fontSize: 24,
    marginRight: 12,
  },
  recommendedInfo: {
    flex: 1,
  },
  recommendedTitle: {
    fontSize: 14,
    fontWeight: '600',
    color: '#333',
    marginBottom: 2,
  },
  recommendedCategory: {
    fontSize: 12,
    color: '#999',
  },
  recommendedArrow: {
    fontSize: 16,
    color: '#999',
  },
});
