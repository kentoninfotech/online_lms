/**
 * API Service
 * Handles all HTTP requests to the Laravel backend
 */

const API_BASE_URL = process.env.EXPO_PUBLIC_API_URL || 'http://192.168.1.100:8000/api';

class APIService {
  constructor() {
    this.token = null;
    this.baseURL = API_BASE_URL;
  }

  /**
   * Set authorization token
   */
  setToken(token) {
    this.token = token;
  }

  /**
   * Get Axios-like request method
   */
  async request(method, endpoint, data = null, isFormData = false) {
    try {
      const url = `${this.baseURL}${endpoint}`;
      const headers = {
        'Content-Type': isFormData ? 'multipart/form-data' : 'application/json',
        'Accept': 'application/json',
      };

      if (this.token) {
        headers['Authorization'] = `Bearer ${this.token}`;
      }

      const options = {
        method,
        headers,
      };

      if (data) {
        if (isFormData) {
          const formData = new FormData();
          Object.keys(data).forEach(key => {
            formData.append(key, data[key]);
          });
          options.body = formData;
        } else {
          options.body = JSON.stringify(data);
        }
      }

      const response = await fetch(url, options);
      const responseData = await response.json();

      if (!response.ok) {
        throw {
          status: response.status,
          data: responseData,
          message: responseData.message || 'Request failed'
        };
      }

      return responseData;
    } catch (error) {
      throw error;
    }
  }

  /**
   * GET request
   */
  get(endpoint) {
    return this.request('GET', endpoint);
  }

  /**
   * POST request
   */
  post(endpoint, data, isFormData = false) {
    return this.request('POST', endpoint, data, isFormData);
  }

  /**
   * PUT request
   */
  put(endpoint, data, isFormData = false) {
    return this.request('PUT', endpoint, data, isFormData);
  }

  /**
   * DELETE request
   */
  delete(endpoint) {
    return this.request('DELETE', endpoint);
  }

  // ==================== AUTH ENDPOINTS ====================

  register(userData) {
    return this.post('/auth/register', userData);
  }

  login(email, password) {
    return this.post('/auth/login', { email, password });
  }

  logout() {
    return this.post('/auth/logout', {});
  }

  verifyEmail(token) {
    return this.post('/auth/verify-email', { token });
  }

  resendVerificationEmail(email) {
    return this.post('/auth/resend-verification', { email });
  }

  forgotPassword(email) {
    return this.post('/auth/forgot-password', { email });
  }

  resetPassword(token, email, password, passwordConfirmation) {
    return this.post('/auth/reset-password', {
      token,
      email,
      password,
      password_confirmation: passwordConfirmation
    });
  }

  // ==================== COURSES ENDPOINTS ====================

  getCourses(page = 1, filters = {}) {
    const queryString = new URLSearchParams({ page, ...filters }).toString();
    return this.get(`/courses?${queryString}`);
  }

  getCourseById(id) {
    return this.get(`/courses/${id}`);
  }

  searchCourses(query) {
    return this.get(`/courses/search?q=${query}`);
  }

  enrollCourse(courseId) {
    return this.post(`/courses/${courseId}/enroll`, {});
  }

  // ==================== LESSONS ENDPOINTS ====================

  getCourseLessons(courseId) {
    return this.get(`/courses/${courseId}/lessons`);
  }

  getLessonById(id) {
    return this.get(`/lessons/${id}`);
  }

  completeLessonQuiz(lessonId, answers) {
    return this.post(`/lessons/${lessonId}/complete`, { answers });
  }

  // ==================== ZOOM SESSIONS ENDPOINTS ====================

  getZoomSessions() {
    return this.get('/zoom-sessions');
  }

  getZoomSessionById(id) {
    return this.get(`/zoom-sessions/${id}`);
  }

  joinZoomSession(sessionId) {
    return this.post(`/zoom-sessions/${sessionId}/join`, {});
  }

  requestReschedule(sessionId, reason) {
    return this.post(`/zoom-sessions/${sessionId}/reschedule`, { reason });
  }

  // ==================== SUBSCRIPTIONS ENDPOINTS ====================

  getSubscriptions() {
    return this.get('/subscriptions');
  }

  getAvailablePlans() {
    return this.get('/plans');
  }

  subscribeToPlan(planId, paymentMethod) {
    return this.post('/subscriptions', { plan_id: planId, payment_method: paymentMethod });
  }

  cancelSubscription(subscriptionId) {
    return this.delete(`/subscriptions/${subscriptionId}`);
  }

  // ==================== PAYMENTS ENDPOINTS ====================

  getPaymentHistory() {
    return this.get('/payments');
  }

  initiatePayment(amount, method) {
    return this.post('/payments', { amount, payment_method: method });
  }

  verifyPayment(transactionId) {
    return this.get(`/payments/${transactionId}/verify`);
  }

  // ==================== USER PROFILE ENDPOINTS ====================

  getProfile() {
    return this.get('/profile');
  }

  updateProfile(profileData) {
    return this.put('/profile', profileData);
  }

  updatePassword(currentPassword, newPassword, confirmPassword) {
    return this.put('/profile/password', {
      current_password: currentPassword,
      password: newPassword,
      password_confirmation: confirmPassword
    });
  }

  uploadProfilePicture(imageUri) {
    const formData = {
      avatar: {
        uri: imageUri,
        type: 'image/jpeg',
        name: 'profile.jpg'
      }
    };
    return this.post('/profile/avatar', formData, true);
  }

  // ==================== DASHBOARD ENDPOINTS ====================

  getDashboard() {
    return this.get('/dashboard');
  }

  // ==================== NOTIFICATIONS ENDPOINTS ====================

  getNotifications() {
    return this.get('/notifications');
  }

  markNotificationAsRead(notificationId) {
    return this.put(`/notifications/${notificationId}/read`, {});
  }

  // ==================== ADMIN ENDPOINTS ====================

  // Users Management
  getUsers(page = 1) {
    return this.get(`/admin/users?page=${page}`);
  }

  createUser(userData) {
    return this.post('/admin/users', userData);
  }

  updateUser(userId, userData) {
    return this.put(`/admin/users/${userId}`, userData);
  }

  deleteUser(userId) {
    return this.delete(`/admin/users/${userId}`);
  }

  // Courses Management
  createCourse(courseData) {
    return this.post('/admin/courses', courseData);
  }

  updateCourse(courseId, courseData) {
    return this.put(`/admin/courses/${courseId}`, courseData);
  }

  deleteCourse(courseId) {
    return this.delete(`/admin/courses/${courseId}`);
  }

  // Lessons Management
  createLesson(courseId, lessonData) {
    return this.post(`/admin/courses/${courseId}/lessons`, lessonData);
  }

  updateLesson(lessonId, lessonData) {
    return this.put(`/admin/lessons/${lessonId}`, lessonData);
  }

  deleteLesson(lessonId) {
    return this.delete(`/admin/lessons/${lessonId}`);
  }

  // Settings
  getSettings() {
    return this.get('/admin/settings');
  }

  updateSettings(settings) {
    return this.put('/admin/settings', settings);
  }
}

export default new APIService();
