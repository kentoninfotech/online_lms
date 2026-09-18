# Mobile App Setup & Integration Guide

This guide explains how to set up the mobile app and connect it to the Laravel LMS backend.

## Table of Contents
1. [Prerequisites](#prerequisites)
2. [Backend Configuration](#backend-configuration)
3. [Mobile App Installation](#mobile-app-installation)
4. [API Integration](#api-integration)
5. [Running the App](#running-the-app)
6. [Testing](#testing)
7. [Deployment](#deployment)

---

## Prerequisites

### Backend Requirements
- Laravel 11 server running with LMS API endpoints
- CORS enabled for mobile app requests
- All API endpoints documented in the [API Documentation](#api-documentation)
- Database with users, courses, lessons, and other LMS tables

### Development Environment
- **Node.js**: v16.x or higher
- **npm**: v8.x or higher (comes with Node.js)
- **Expo CLI**: Latest version
- **Git**: For version control
- **Device/Emulator**: Android emulator or iOS simulator for testing

### Recommended Testing Tools
- **Postman**: API testing
- **Android Studio**: Android development and emulator
- **Xcode**: iOS development and simulator (Mac only)
- **Expo Go**: Mobile app for quick testing

---

## Backend Configuration

### 1. Enable CORS for Mobile App

Update your Laravel `config/cors.php` or add middleware:

```php
// config/cors.php
return [
    'paths' => ['api/*'],
    'allowed_methods' => ['*'],
    'allowed_origins' => [
        'http://localhost:3000',
        'http://192.168.1.*',      // Your local network
        'https://yourserver.com',   // Production server
    ],
    'allowed_origins_patterns' => ['*'],
    'allowed_headers' => ['*'],
    'exposed_headers' => ['*'],
    'max_age' => 0,
    'supports_credentials' => true,
];
```

### 2. Ensure API Endpoints Return Required Data

All endpoints should return JSON with proper structure:

```json
{
  "data": { /* resource data */ },
  "message": "Success message",
  "status": "success"
}
```

### 3. Update Authentication Middleware

Ensure API routes are protected with proper middleware:

```php
Route::middleware(['auth:sanctum', 'verified'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'show']);
    Route::get('/dashboard', [DashboardController::class, 'index']);
    // ... other protected routes
});
```

### 4. Configure Token Expiration

For mobile apps, configure appropriate token expiration in `config/sanctum.php`:

```php
'expiration' => 60 * 24 * 7,  // 7 days
```

---

## Mobile App Installation

### Step 1: Navigate to Mobile App Directory

```bash
cd mobile-app
```

### Step 2: Install Dependencies

```bash
npm install
```

### Step 3: Install Required Packages

```bash
npm install axios react-native-screens react-native-safe-area-context \
  react-native-gesture-handler @react-native-async-storage/async-storage \
  expo-secure-store expo-local-authentication expo-image-picker
```

### Step 4: Create Environment File

```bash
cp .env.example .env
```

Edit `.env` and update your backend URL:

```env
EXPO_PUBLIC_API_URL=http://192.168.1.100:8000/api
```

Replace `192.168.1.100` with your:
- **Host machine IP** for Android emulator
- **Local network IP** for physical devices
- **`localhost` or `127.0.0.1`** for iOS simulator (or your machine IP)

---

## API Integration

### API Service Architecture

The app uses a centralized API service (`src/services/api.js`) that handles:

1. **Request Configuration**
   - Base URL from environment
   - Authorization headers
   - Content-Type handling

2. **Response Handling**
   - JSON parsing
   - Error throwing with meaningful messages
   - Token management

3. **Retry Logic** (To be implemented)
   - Automatic retry on network failures
   - Exponential backoff

### Key API Methods

#### Authentication
```javascript
// Login
await APIService.login('email@example.com', 'password')
// Returns: { token, user, data }

// Register
await APIService.register({
  name: 'User Name',
  email: 'email@example.com',
  password: 'password',
  role: 'learner'
})

// Logout
await APIService.logout()
```

#### Courses
```javascript
// Get all courses
await APIService.getCourses(page = 1, filters = {})
// Returns: { data: [...courses] }

// Get course details
await APIService.getCourseById(courseId)
// Returns: { data: {...course} }

// Enroll in course
await APIService.enrollCourse(courseId)
// Returns: { message: "Enrolled successfully" }
```

#### Lessons
```javascript
// Get lessons for course
await APIService.getCourseLessons(courseId)
// Returns: { data: [...lessons] }

// Get lesson details
await APIService.getLessonById(lessonId)
// Returns: { data: {...lesson} }

// Complete lesson quiz
await APIService.completeLessonQuiz(lessonId, answers)
// answers format: { [questionId]: answer_value }
```

#### User Profile
```javascript
// Get profile
await APIService.getProfile()
// Returns: { data: {...user} }

// Update profile
await APIService.updateProfile({
  name: 'New Name',
  email: 'newemail@example.com'
})

// Update password
await APIService.updatePassword(currentPwd, newPwd, confirmPwd)

// Upload avatar
await APIService.uploadProfilePicture(imageUri)
```

### Expected Response Formats

#### Courses List
```json
{
  "data": [
    {
      "id": 1,
      "title": "Course Title",
      "description": "Description",
      "image": "https://...",
      "price": 9.99,
      "level": "Beginner",
      "lessons_count": 10,
      "students_count": 150,
      "is_enrolled": false,
      "rating": 4.5
    }
  ],
  "pagination": {
    "current_page": 1,
    "total": 50
  }
}
```

#### Course Details
```json
{
  "data": {
    "id": 1,
    "title": "Course Title",
    "full_description": "Long description",
    "image": "https://...",
    "lessons": [
      {
        "id": 1,
        "title": "Lesson 1",
        "duration": 30,
        "video_url": "https://..."
      }
    ],
    "instructor": {
      "id": 1,
      "name": "Instructor Name"
    },
    "enrolled": true
  }
}
```

#### Lesson Details
```json
{
  "data": {
    "id": 1,
    "title": "Lesson Title",
    "content": "Lesson content",
    "video_url": "https://...",
    "resources": [
      {
        "title": "Resource Title",
        "url": "https://..."
      }
    ],
    "quiz": {
      "id": 1,
      "title": "Quiz Title",
      "questions": [
        {
          "id": 1,
          "question": "Question text?",
          "type": "multiple_choice",
          "options": ["Option 1", "Option 2"]
        }
      ]
    }
  }
}
```

---

## Running the App

### Development Mode

```bash
npm start
```

This opens the Expo CLI menu with options:

- **Type `i`**: Open in iOS Simulator (Mac only)
- **Type `a`**: Open in Android Emulator
- **Scan QR Code**: Use Expo Go app on your device

### iOS Simulator
```bash
npm run ios
```

### Android Emulator
```bash
npm run android
```

### Web Preview (Testing API)
```bash
npm run web
```

---

## Testing

### 1. Test Authentication Flow

```
1. Launch app
2. Tap "Sign Up"
3. Enter:
   - Name: Test User
   - Email: test@example.com
   - Role: Learner
   - Password: Password123!
4. Check backend database for new user
5. Return to login
6. Enter credentials and tap "Sign In"
7. Should see Dashboard
```

### 2. Test Course Browsing

```
1. From Dashboard, tap "Browse Courses" or go to Courses tab
2. Verify courses load from API
3. Search for course
4. Tap on course to see details
5. Verify lessons display
6. Tap "Enroll" button
7. Check enrollment in backend database
```

### 3. Test Lesson & Quiz

```
1. Navigate to enrolled course
2. Tap on lesson
3. Verify video displays
4. Scroll to quiz section
5. Answer all questions
6. Tap "Submit Quiz"
7. Verify completion message
8. Check progress updated in backend
```

### 4. Test Profile Management

```
1. Go to Profile tab
2. Update name/email
3. Tap "Save Changes"
4. Verify changes in backend
5. Switch to Password tab
6. Change password (old → new → confirm new)
7. Logout and login with new password
8. Verify authentication works
```

### 5. Test Error Scenarios

```
1. Try login with wrong email → Should show error
2. Try login with wrong password → Should show error
3. Try register with existing email → Should show error
4. Try register with weak password → Should show requirements
5. Unplug network and try API call → Should queue offline
6. Reconnect network → Should sync queued requests
```

### Using Postman for API Testing

1. Import API endpoints to Postman
2. Create environment with base URL
3. Test each endpoint manually
4. Verify response formats
5. Check error handling

---

## Deployment

### Building for Android

```bash
# Requires EAS CLI
npm install -g eas-cli

# Login to Expo
eas login

# Build APK
eas build --platform android --type apk

# The APK can be distributed via:
# - Google Play Store
# - Direct download
# - Firebase App Distribution
# - Your server
```

### Building for iOS

```bash
# Build for App Store
eas build --platform ios

# The IPA can be distributed via:
# - Apple App Store
# - TestFlight for beta testing
```

### Preparing for Submission

1. **App Store**
   - Create Apple Developer account
   - Configure bundle ID: `com.yourcompany.lmsmobile`
   - Follow App Store submission guidelines

2. **Google Play Store**
   - Create Google Developer account
   - Generate keystore for signing
   - Follow Play Store publication guidelines

3. **Version Management**
   - Update version in `app.json`
   - Update version in `package.json`
   - Create release notes

### Production Configuration

Update `.env` for production:

```env
EXPO_PUBLIC_API_URL=https://api.yourdomain.com/api
EXPO_PUBLIC_DEBUG=false
```

---

## Troubleshooting

### "Unable to resolve module"

```bash
# Clear cache and reinstall
rm -rf node_modules package-lock.json
npm install
npm start -- --clear
```

### "API connection refused"

1. Check backend URL in `.env`
2. Verify backend server is running
3. Check CORS configuration
4. Ensure device/emulator can reach backend
5. Use `adb logcat` for Android debugging

### "Authentication failed"

1. Verify user exists in database
2. Check password is correct
3. Verify email is verified (if required)
4. Check token expiration configuration
5. Review auth middleware in backend

### "Video not playing"

1. Verify video URL format
2. Check video file exists on server
3. Test URL in browser
4. Verify video permissions
5. Check WebView configuration

### "Slow performance"

1. Use Android Profiler/Xcode Instruments
2. Check FlatList keyExtractor
3. Optimize image sizes
4. Reduce API response payload
5. Implement pagination/lazy loading

---

## Additional Resources

- [Expo Documentation](https://docs.expo.dev/)
- [React Native Docs](https://reactnative.dev/docs)
- [React Navigation](https://reactnavigation.org/)
- [Android Studio Emulator](https://developer.android.com/studio)
- [Xcode Simulator](https://developer.apple.com/xcode/)

---

## Support & Contact

For issues related to:
- **Mobile app code**: Check `src/` directory
- **API integration**: Review `src/services/api.js`
- **Backend API**: Check Laravel LMS backend
- **Authentication**: See `src/context/AuthContext.js`

