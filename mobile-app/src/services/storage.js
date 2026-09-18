/**
 * Storage Service
 * Handles local data persistence
 */

import AsyncStorage from '@react-native-async-storage/async-storage';

const KEYS = {
  AUTH_TOKEN: '@lms_auth_token',
  USER_DATA: '@lms_user_data',
  REFRESH_TOKEN: '@lms_refresh_token',
  COURSES_CACHE: '@lms_courses_cache',
  LESSONS_CACHE: '@lms_lessons_cache',
  USER_PREFERENCES: '@lms_user_preferences',
  OFFLINE_QUEUE: '@lms_offline_queue',
};

class StorageService {
  /**
   * Save authentication token
   */
  async saveToken(token) {
    try {
      await AsyncStorage.setItem(KEYS.AUTH_TOKEN, token);
    } catch (error) {
      console.error('Error saving token:', error);
      throw error;
    }
  }

  /**
   * Get authentication token
   */
  async getToken() {
    try {
      return await AsyncStorage.getItem(KEYS.AUTH_TOKEN);
    } catch (error) {
      console.error('Error retrieving token:', error);
      return null;
    }
  }

  /**
   * Save user data
   */
  async saveUser(userData) {
    try {
      await AsyncStorage.setItem(KEYS.USER_DATA, JSON.stringify(userData));
    } catch (error) {
      console.error('Error saving user data:', error);
      throw error;
    }
  }

  /**
   * Get user data
   */
  async getUser() {
    try {
      const userData = await AsyncStorage.getItem(KEYS.USER_DATA);
      return userData ? JSON.parse(userData) : null;
    } catch (error) {
      console.error('Error retrieving user data:', error);
      return null;
    }
  }

  /**
   * Save refresh token
   */
  async saveRefreshToken(token) {
    try {
      await AsyncStorage.setItem(KEYS.REFRESH_TOKEN, token);
    } catch (error) {
      console.error('Error saving refresh token:', error);
      throw error;
    }
  }

  /**
   * Get refresh token
   */
  async getRefreshToken() {
    try {
      return await AsyncStorage.getItem(KEYS.REFRESH_TOKEN);
    } catch (error) {
      console.error('Error retrieving refresh token:', error);
      return null;
    }
  }

  /**
   * Cache courses data
   */
  async cacheCourses(courses) {
    try {
      await AsyncStorage.setItem(KEYS.COURSES_CACHE, JSON.stringify(courses));
    } catch (error) {
      console.error('Error caching courses:', error);
    }
  }

  /**
   * Get cached courses
   */
  async getCachedCourses() {
    try {
      const courses = await AsyncStorage.getItem(KEYS.COURSES_CACHE);
      return courses ? JSON.parse(courses) : null;
    } catch (error) {
      console.error('Error retrieving cached courses:', error);
      return null;
    }
  }

  /**
   * Cache lessons for a course
   */
  async cacheLessons(courseId, lessons) {
    try {
      const key = `${KEYS.LESSONS_CACHE}_${courseId}`;
      await AsyncStorage.setItem(key, JSON.stringify(lessons));
    } catch (error) {
      console.error('Error caching lessons:', error);
    }
  }

  /**
   * Get cached lessons for a course
   */
  async getCachedLessons(courseId) {
    try {
      const key = `${KEYS.LESSONS_CACHE}_${courseId}`;
      const lessons = await AsyncStorage.getItem(key);
      return lessons ? JSON.parse(lessons) : null;
    } catch (error) {
      console.error('Error retrieving cached lessons:', error);
      return null;
    }
  }

  /**
   * Save user preferences
   */
  async savePreferences(preferences) {
    try {
      await AsyncStorage.setItem(KEYS.USER_PREFERENCES, JSON.stringify(preferences));
    } catch (error) {
      console.error('Error saving preferences:', error);
    }
  }

  /**
   * Get user preferences
   */
  async getPreferences() {
    try {
      const prefs = await AsyncStorage.getItem(KEYS.USER_PREFERENCES);
      return prefs ? JSON.parse(prefs) : {};
    } catch (error) {
      console.error('Error retrieving preferences:', error);
      return {};
    }
  }

  /**
   * Add request to offline queue
   */
  async addToOfflineQueue(request) {
    try {
      const queue = await this.getOfflineQueue();
      queue.push({ ...request, timestamp: new Date().toISOString() });
      await AsyncStorage.setItem(KEYS.OFFLINE_QUEUE, JSON.stringify(queue));
    } catch (error) {
      console.error('Error adding to offline queue:', error);
    }
  }

  /**
   * Get offline queue
   */
  async getOfflineQueue() {
    try {
      const queue = await AsyncStorage.getItem(KEYS.OFFLINE_QUEUE);
      return queue ? JSON.parse(queue) : [];
    } catch (error) {
      console.error('Error retrieving offline queue:', error);
      return [];
    }
  }

  /**
   * Clear offline queue
   */
  async clearOfflineQueue() {
    try {
      await AsyncStorage.removeItem(KEYS.OFFLINE_QUEUE);
    } catch (error) {
      console.error('Error clearing offline queue:', error);
    }
  }

  /**
   * Clear all stored data (logout)
   */
  async clearAll() {
    try {
      await AsyncStorage.multiRemove([
        KEYS.AUTH_TOKEN,
        KEYS.USER_DATA,
        KEYS.REFRESH_TOKEN,
        KEYS.COURSES_CACHE,
        KEYS.USER_PREFERENCES,
      ]);
    } catch (error) {
      console.error('Error clearing storage:', error);
      throw error;
    }
  }
}

export default new StorageService();
