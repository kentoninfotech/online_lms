/**
 * Lesson Detail Screen
 * Display individual lesson with video and quiz
 */

import React, { useState, useEffect } from 'react';
import {
  View,
  ScrollView,
  TouchableOpacity,
  Text,
  StyleSheet,
  ActivityIndicator,
  WebView,
  Alert,
} from 'react-native';
import APIService from '../services/api';

export default function LessonDetailScreen({ route, navigation }) {
  const { lessonId } = route.params;
  const [lesson, setLesson] = useState(null);
  const [loading, setLoading] = useState(true);
  const [completing, setCompleting] = useState(false);
  const [quiz, setQuiz] = useState(null);
  const [answers, setAnswers] = useState({});
  const [showConclusion, setShowConclusion] = useState(false);
  const [error, setError] = useState(null);

  useEffect(() => {
    fetchLessonDetail();
  }, [lessonId]);

  const fetchLessonDetail = async () => {
    try {
      setLoading(true);
      setError(null);

      const response = await APIService.getLessonById(lessonId);
      const lessonData = response.data || response.lesson;
      setLesson(lessonData);

      if (lessonData.quiz) {
        setQuiz(lessonData.quiz);
        const initialAnswers = {};
        if (lessonData.quiz.questions) {
          lessonData.quiz.questions.forEach(q => {
            initialAnswers[q.id] = null;
          });
        }
        setAnswers(initialAnswers);
      }
    } catch (err) {
      setError('Failed to load lesson. Please try again.');
      console.error('Error fetching lesson:', err);
    } finally {
      setLoading(false);
    }
  };

  const handleSelectAnswer = (questionId, answerValue) => {
    setAnswers(prev => ({
      ...prev,
      [questionId]: answerValue
    }));
  };

  const handleCompleteQuiz = async () => {
    if (!quiz) return;

    // Validate all questions answered
    const unanswered = quiz.questions.filter(q => !answers[q.id]);
    if (unanswered.length > 0) {
      Alert.alert('Incomplete', 'Please answer all questions before submitting');
      return;
    }

    try {
      setCompleting(true);
      const payload = {
        answers: Object.entries(answers).reduce((acc, [qId, answer]) => {
          acc[qId] = answer;
          return acc;
        }, {})
      };

      await APIService.completeLessonQuiz(lessonId, payload);
      setShowConclusion(true);
      Alert.alert('Success', 'Quiz completed successfully!');
    } catch (err) {
      Alert.alert('Error', err.data?.message || 'Failed to submit quiz');
    } finally {
      setCompleting(false);
    }
  };

  if (loading) {
    return (
      <View style={styles.centerContainer}>
        <ActivityIndicator size="large" color="#007AFF" />
      </View>
    );
  }

  if (!lesson) {
    return (
      <View style={styles.centerContainer}>
        <Text style={styles.errorText}>Lesson not found</Text>
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
      {/* Lesson Title */}
      <View style={styles.header}>
        <Text style={styles.title}>{lesson.title}</Text>
      </View>

      {/* Video Section */}
      {lesson.video_url && (
        <View style={styles.videoContainer}>
          <WebView
            source={{ uri: lesson.video_url }}
            style={styles.video}
            allowsFullscreenVideo
          />
        </View>
      )}

      {/* Lesson Content */}
      {lesson.content && (
        <View style={styles.section}>
          <Text style={styles.sectionTitle}>Content</Text>
          <Text style={styles.content}>{lesson.content}</Text>
        </View>
      )}

      {/* Resources */}
      {lesson.resources && lesson.resources.length > 0 && (
        <View style={styles.section}>
          <Text style={styles.sectionTitle}>Resources</Text>
          {lesson.resources.map((resource, index) => (
            <TouchableOpacity
              key={index}
              style={styles.resourceButton}
              onPress={() => {
                // In a real app, you would open the resource URL
                Alert.alert('Resource', `Opening: ${resource.title}`);
              }}
            >
              <Text style={styles.resourceIcon}>📄</Text>
              <Text style={styles.resourceTitle}>{resource.title}</Text>
              <Text style={styles.resourceArrow}>→</Text>
            </TouchableOpacity>
          ))}
        </View>
      )}

      {/* Quiz Section */}
      {quiz && !showConclusion && (
        <View style={styles.section}>
          <Text style={styles.sectionTitle}>Quiz: {quiz.title}</Text>
          <Text style={styles.quizDescription}>{quiz.description}</Text>

          {quiz.questions && quiz.questions.map((question, qIndex) => (
            <View key={question.id} style={styles.questionContainer}>
              <Text style={styles.questionNumber}>Question {qIndex + 1}</Text>
              <Text style={styles.questionText}>{question.question}</Text>

              {question.type === 'multiple_choice' && question.options && (
                <View style={styles.optionsContainer}>
                  {question.options.map((option, oIndex) => (
                    <TouchableOpacity
                      key={oIndex}
                      style={[
                        styles.optionButton,
                        answers[question.id] === option && styles.optionSelected
                      ]}
                      onPress={() => handleSelectAnswer(question.id, option)}
                    >
                      <View style={styles.optionDot}>
                        {answers[question.id] === option && (
                          <View style={styles.optionDotFilled} />
                        )}
                      </View>
                      <Text style={styles.optionText}>{option}</Text>
                    </TouchableOpacity>
                  ))}
                </View>
              )}

              {question.type === 'true_false' && (
                <View style={styles.trueFalseContainer}>
                  <TouchableOpacity
                    style={[
                      styles.trueFalseButton,
                      answers[question.id] === true && styles.trueFalseSelected
                    ]}
                    onPress={() => handleSelectAnswer(question.id, true)}
                  >
                    <Text style={styles.trueFalseButtonText}>True</Text>
                  </TouchableOpacity>
                  <TouchableOpacity
                    style={[
                      styles.trueFalseButton,
                      answers[question.id] === false && styles.trueFalseSelected
                    ]}
                    onPress={() => handleSelectAnswer(question.id, false)}
                  >
                    <Text style={styles.trueFalseButtonText}>False</Text>
                  </TouchableOpacity>
                </View>
              )}
            </View>
          ))}

          <TouchableOpacity
            style={[styles.submitButton, completing && styles.disabledButton]}
            onPress={handleCompleteQuiz}
            disabled={completing}
          >
            {completing ? (
              <ActivityIndicator color="#fff" />
            ) : (
              <Text style={styles.submitButtonText}>Submit Quiz</Text>
            )}
          </TouchableOpacity>
        </View>
      )}

      {/* Completion Message */}
      {(showConclusion || lesson.completed) && (
        <View style={styles.completionContainer}>
          <Text style={styles.completionIcon}>✅</Text>
          <Text style={styles.completionTitle}>Lesson Completed!</Text>
          <Text style={styles.completionText}>
            Great job! You've successfully completed this lesson.
          </Text>
          <TouchableOpacity
            style={styles.nextButton}
            onPress={() => navigation.goBack()}
          >
            <Text style={styles.nextButtonText}>Continue to Course</Text>
          </TouchableOpacity>
        </View>
      )}

      {error && (
        <View style={styles.errorContainer}>
          <Text style={styles.errorText}>{error}</Text>
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
    backgroundColor: '#fff',
    paddingHorizontal: 16,
    paddingVertical: 16,
    borderBottomWidth: 1,
    borderBottomColor: '#e0e0e0',
  },
  title: {
    fontSize: 22,
    fontWeight: 'bold',
    color: '#333',
  },
  videoContainer: {
    backgroundColor: '#000',
    height: 220,
    marginVertical: 8,
  },
  video: {
    flex: 1,
  },
  section: {
    backgroundColor: '#fff',
    marginHorizontal: 12,
    marginVertical: 8,
    paddingHorizontal: 16,
    paddingVertical: 12,
    borderRadius: 8,
  },
  sectionTitle: {
    fontSize: 16,
    fontWeight: 'bold',
    color: '#333',
    marginBottom: 12,
  },
  content: {
    fontSize: 14,
    color: '#666',
    lineHeight: 20,
  },
  resourceButton: {
    flexDirection: 'row',
    alignItems: 'center',
    paddingVertical: 12,
    paddingHorizontal: 12,
    backgroundColor: '#f0f0f0',
    borderRadius: 6,
    marginBottom: 8,
  },
  resourceIcon: {
    fontSize: 20,
    marginRight: 12,
  },
  resourceTitle: {
    flex: 1,
    fontSize: 14,
    color: '#333',
    fontWeight: '500',
  },
  resourceArrow: {
    fontSize: 16,
    color: '#007AFF',
  },
  quizDescription: {
    fontSize: 14,
    color: '#666',
    marginBottom: 16,
  },
  questionContainer: {
    marginBottom: 20,
    paddingBottom: 16,
    borderBottomWidth: 1,
    borderBottomColor: '#e0e0e0',
  },
  questionNumber: {
    fontSize: 12,
    color: '#999',
    fontWeight: '600',
    marginBottom: 4,
  },
  questionText: {
    fontSize: 15,
    fontWeight: '600',
    color: '#333',
    marginBottom: 12,
  },
  optionsContainer: {
    gap: 10,
  },
  optionButton: {
    flexDirection: 'row',
    alignItems: 'center',
    paddingVertical: 12,
    paddingHorizontal: 12,
    backgroundColor: '#f9f9f9',
    borderRadius: 6,
    borderWidth: 1,
    borderColor: '#ddd',
  },
  optionSelected: {
    backgroundColor: '#e3f2fd',
    borderColor: '#007AFF',
  },
  optionDot: {
    width: 20,
    height: 20,
    borderRadius: 10,
    borderWidth: 2,
    borderColor: '#ddd',
    justifyContent: 'center',
    alignItems: 'center',
    marginRight: 12,
  },
  optionDotFilled: {
    width: 10,
    height: 10,
    borderRadius: 5,
    backgroundColor: '#007AFF',
  },
  optionText: {
    flex: 1,
    fontSize: 14,
    color: '#333',
  },
  trueFalseContainer: {
    flexDirection: 'row',
    gap: 12,
  },
  trueFalseButton: {
    flex: 1,
    paddingVertical: 12,
    backgroundColor: '#f9f9f9',
    borderRadius: 6,
    borderWidth: 1,
    borderColor: '#ddd',
    alignItems: 'center',
  },
  trueFalseSelected: {
    backgroundColor: '#007AFF',
    borderColor: '#007AFF',
  },
  trueFalseButtonText: {
    fontSize: 14,
    fontWeight: '600',
    color: '#333',
  },
  submitButton: {
    backgroundColor: '#007AFF',
    paddingVertical: 14,
    borderRadius: 8,
    alignItems: 'center',
    marginTop: 16,
  },
  disabledButton: {
    opacity: 0.6,
  },
  submitButtonText: {
    color: '#fff',
    fontSize: 16,
    fontWeight: 'bold',
  },
  completionContainer: {
    alignItems: 'center',
    paddingVertical: 40,
    paddingHorizontal: 20,
  },
  completionIcon: {
    fontSize: 64,
    marginBottom: 16,
  },
  completionTitle: {
    fontSize: 24,
    fontWeight: 'bold',
    color: '#333',
    marginBottom: 8,
  },
  completionText: {
    fontSize: 16,
    color: '#666',
    textAlign: 'center',
    marginBottom: 24,
  },
  nextButton: {
    backgroundColor: '#007AFF',
    paddingHorizontal: 32,
    paddingVertical: 12,
    borderRadius: 6,
  },
  nextButtonText: {
    color: '#fff',
    fontSize: 14,
    fontWeight: '600',
  },
  errorContainer: {
    backgroundColor: '#fee',
    marginHorizontal: 12,
    marginVertical: 12,
    paddingHorizontal: 16,
    paddingVertical: 12,
    borderRadius: 8,
  },
  errorText: {
    color: '#721c24',
    fontSize: 14,
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
