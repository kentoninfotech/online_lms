# LMS Mobile App

A comprehensive React Native/Expo mobile application for learners and tutors to access the LMS platform on Android and iOS devices.

## ✨ Features

### For Learners
- **User Authentication**: Secure login and registration with email verification
- **Browse Courses**: Search, filter, and discover available courses
- **Enroll in Courses**: One-tap enrollment in courses
- **Learn Lessons**: Access organized lesson content with video playback
- **Complete Quizzes**: Interactive quizzes with multiple question types
- **Track Progress**: Monitor learning progress with completion percentages
- **Live Sessions**: Join Zoom sessions with instructors
- **Manage Subscription**: View and manage active subscriptions
- **User Profile**: Update profile information and manage account settings

### For Tutors
- **Manage Courses**: Create, edit, and delete courses
- **Create Lessons**: Add lessons with content, videos, and quizzes
- **Manage Students**: View enrolled students and their progress
- **Live Teaching**: Host live sessions with students
- **Analytics**: View student engagement and completion rates
- **Profile Management**: Manage tutor information and credentials

### For All Users
- **Offline Support**: Download content for offline learning (coming soon)
- **Notifications**: Real-time push notifications for updates
- **Secure Storage**: Encrypted token and credential storage
- **Multi-language**: Localization support (coming soon)
- **Dark Mode**: Theme customization (coming soon)

## 📱 Requirements

- **Node.js**: v16 or higher
- **npm** or **yarn**: Package managers
- **Expo CLI**: `npm install -g expo-cli`
- **Android/iOS Device** or **Emulator** for testing
- **Internet Connection**: For API communication with Laravel backend

## 🚀 Installation & Setup

### 1. Install Dependencies

```bash
cd mobile-app
npm install
# or
yarn install
```

### 2. Install Additional Packages

```bash
npm install axios react-native-screens react-native-safe-area-context \
  react-native-gesture-handler @react-native-async-storage/async-storage \
  expo-secure-store expo-local-authentication expo-image-picker
```

### 3. Configure API Base URL

Create or update `.env` file in the `mobile-app` directory:

```env
EXPO_PUBLIC_API_URL=http://192.168.1.100:8000/api
```

Replace `192.168.1.100` with your Laravel backend server IP address.

### 4. Run the Application

#### For Development

```bash
npm start
# or
yarn start
```

This will open the Expo CLI menu where you can:
- Press `i` to open in iOS Simulator
- Press `a` to open in Android Emulator
- Scan QR code with Expo Go app on your mobile device

#### For Production Build

```bash
# Android APK
expo build:android -t apk

# iOS IPA
expo build:ios
```

## 📁 Project Structure

```
mobile-app/
├── src/
│   ├── services/
│   │   ├── api.js              # API communication
│   │   └── storage.js          # Local storage management
│   ├── context/
│   │   └── AuthContext.js      # Authentication state management
│   ├── screens/
│   │   ├── LoginScreen.js
│   │   ├── RegisterScreen.js
│   │   ├── DashboardScreen.js
│   │   ├── CoursesScreen.js
│   │   ├── CourseDetailScreen.js
│   │   ├── LessonDetailScreen.js
│   │   └── ProfileScreen.js
│   ├── navigation/
│   │   └── RootNavigator.js    # Navigation structure
│   ├── components/             # Reusable components
│   └── utils/                  # Utility functions
├── app/
│   └── _layout.tsx            # Root layout
├── package.json
├── app.json                   # Expo configuration
└── tsconfig.json
```

## 🔑 API Endpoints

The app communicates with your Laravel backend using the following endpoints:

### Authentication
- `POST /api/auth/register` - User registration
- `POST /api/auth/login` - User login
- `POST /api/auth/logout` - User logout
- `POST /api/auth/verify-email` - Email verification
- `POST /api/auth/resend-verification` - Resend verification email
- `POST /api/auth/forgot-password` - Forgotten password request
- `POST /api/auth/reset-password` - Password reset

### Courses
- `GET /api/courses` - List all courses (paginated)
- `GET /api/courses/{id}` - Get course details
- `GET /api/courses/search?q=query` - Search courses
- `POST /api/courses/{id}/enroll` - Enroll in a course

### Lessons
- `GET /api/courses/{id}/lessons` - Get course lessons
- `GET /api/lessons/{id}` - Get lesson details
- `POST /api/lessons/{id}/complete` - Complete lesson quiz

### User
- `GET /api/profile` - Get user profile
- `PUT /api/profile` - Update profile
- `PUT /api/profile/password` - Update password
- `POST /api/profile/avatar` - Upload avatar

### Dashboard
- `GET /api/dashboard` - Get dashboard data

### Admin (Tutor)
- `GET /api/admin/users` - List users
- `POST /api/admin/users` - Create user
- `PUT /api/admin/users/{id}` - Update user
- `DELETE /api/admin/users/{id}` - Delete user
- `GET /api/admin/courses` - List courses
- `POST /api/admin/courses` - Create course
- `PUT /api/admin/courses/{id}` - Update course
- `DELETE /api/admin/courses/{id}` - Delete course

## 🎨 UI Components

The app uses React Native's built-in components with custom styling:

- **Screens**: Full-screen components for different sections
- **Navigation**: Bottom tab navigation + Stack navigation
- **Forms**: TextInput with validation and error messages
- **Buttons**: TouchableOpacity with custom styling
- **Lists**: FlatList for performant scrolling
- **Images**: Network images with fallback placeholders

## 🔐 Security Features

- **Secure Token Storage**: JWT tokens stored in secure async storage
- **HTTPOnly Cookies**: Support for secure cookie-based authentication
- **SSL/TLS**: Encrypted communication with backend
- **Token Refresh**: Automatic token refresh on expiration
- **Input Validation**: Client-side validation of user inputs
- **Permission Management**: Proper permission handling for device features

## 🌐 Offline Support

The app includes provisions for:
- Local caching of courses and lessons
- Offline queue for failed requests
- Cached data display when offline
- Automatic sync when connection restored

## 🚦 State Management

- **Context API**: Auth context for global state
- **AsyncStorage**: Persistent storage for user data
- **Component State**: useState for local component state
- **Custom Hooks**: Custom hooks for reusable logic

## 📲 Push Notifications

The app is configured for push notifications:
- Expo Push Notifications setup (coming soon)
- Background notification handling
- Deep linking from notifications
- Local notifications for reminders

## 🧪 Testing

### Manual Testing

1. **Authentication Flow**
   - Register new account
   - Verify email
   - Login with credentials
   - Update profile
   - Change password
   - Logout

2. **Course Browsing**
   - List and search courses
   - View course details
   - Check lesson content
   - View quizzes

3. **Learning**
   - Enroll in course
   - Access lessons
   - Complete quizzes
   - Track progress

### Automated Testing

```bash
# Run tests
npm test

# Run with coverage
npm test -- --coverage
```

## 🐛 Troubleshooting

### Common Issues

1. **API Connection Failed**
   - Check backend server is running
   - Verify API base URL in .env
   - Ensure device/emulator can reach backend
   - Check network connectivity

2. **Login Not Working**
   - Verify credentials are correct
   - Ensure email is verified
   - Check backend auth configuration
   - Clear app cache and reinstall

3. **Videos Not Playing**
   - Check video URL format
   - Ensure WebView is properly configured
   - Verify video permissions
   - Check internet connectivity

4. **Performance Issues**
   - Check app memory usage
   - Optimize images
   - Use FlatList keyExtractor correctly
   - Profile with React Native Performance Monitor

## 📚 Documentation

- [React Native Documentation](https://reactnative.dev/)
- [Expo Documentation](https://docs.expo.dev/)
- [React Navigation Documentation](https://reactnavigation.org/)
- [AsyncStorage Documentation](https://react-native-async-storage.github.io/)

## 🤝 Contributing

This is a mobile client for the LMS platform. For contributions:

1. Follow React Native best practices
2. Maintain code style consistency
3. Add appropriate comments
4. Test before submitting
5. Update documentation

## 📄 License

This project is part of the LMS platform and follows the same license terms.

## 📞 Support

For issues and feature requests:
- Check the main LMS repository documentation
- Review API documentation
- Contact the development team

## 🔄 Sync with Backend

The mobile app automatically:
- Syncs user data on login
- Caches courses and lessons locally
- Queues failed requests for retry
- Updates profile information
- Tracks progress completion

## 🎯 Roadmap

- [ ] Offline learning with content download
- [ ] Push notifications integration
- [ ] Multi-language support
- [ ] Dark mode theme
- [ ] Video playback with variable speed
- [ ] Advanced quiz types
- [ ] Student assignment submission
- [ ] Certificate generation
- [ ] Star ratings and reviews
- [ ] Social learning features

## 📝 Version History

**v1.0.0** (Initial Release)
- Core features implementation
- Auth and course management
- Lesson playback and quizzes
- User profile management
