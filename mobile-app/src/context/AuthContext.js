/**
 * Authentication Context
 * Manages authentication state and user data
 */

import React, { createContext, useState, useEffect, useCallback } from 'react';
import APIService from '../services/api';
import StorageService from '../services/storage';

export const AuthContext = createContext();

export const AuthProvider = ({ children }) => {
  const [state, dispatch] = useState({
    isLoading: true,
    isSignout: false,
    user: null,
    token: null,
    userRole: null,
  });

  const [error, setError] = useState(null);

  // Bootstrap async data when app starts
  useEffect(() => {
    const bootstrapAsync = async () => {
      try {
        const token = await StorageService.getToken();
        const user = await StorageService.getUser();

        if (token && user) {
          APIService.setToken(token);
          dispatch({
            type: 'RESTORE_TOKEN',
            token,
            user,
            userRole: user.role || null,
          });
        } else {
          dispatch({ type: 'SIGN_OUT' });
        }
      } catch (e) {
        console.error('Error bootstrapping auth:', e);
        dispatch({ type: 'SIGN_OUT' });
      }
    };

    bootstrapAsync();
  }, []);

  const authContext = {
    state,
    signIn: useCallback(
      async (email, password) => {
        try {
          setError(null);
          const response = await APIService.login(email, password);

          // Handle unverified email response
          if (response.email_verified === false) {
            const unverifiedError = 'Please verify your email before logging in.';
            setError(unverifiedError);
            return {
              success: false,
              error: unverifiedError,
              email_not_verified: true,
              user: response.user
            };
          }

          if (response.token) {
            const userData = response.user || response.data?.user;
            await StorageService.saveToken(response.token);
            await StorageService.saveUser(userData);
            APIService.setToken(response.token);

            dispatch({
              type: 'SIGN_IN',
              token: response.token,
              user: userData,
              userRole: userData.role || null,
            });

            return { success: true, data: userData };
          }
        } catch (err) {
          const errorMessage = err.data?.message || 'Login failed. Please try again.';
          setError(errorMessage);
          
          // Check if error is due to unverified email
          if (err.status === 403 && err.data?.email_verified === false) {
            return {
              success: false,
              error: errorMessage,
              email_not_verified: true,
              user: err.data?.user
            };
          }
          
          return { success: false, error: errorMessage };
        }
      },
      []
    ),

    signUp: useCallback(
      async (userData) => {
        try {
          setError(null);
          const response = await APIService.register(userData);
          return { success: true, data: response };
        } catch (err) {
          const errorMessage = err.data?.message || 'Registration failed. Please try again.';
          setError(errorMessage);
          return { success: false, error: errorMessage };
        }
      },
      []
    ),

    signOut: useCallback(
      async () => {
        try {
          await APIService.logout();
        } catch (err) {
          console.error('Logout API error:', err);
        } finally {
          await StorageService.clearAll();
          APIService.setToken(null);
          dispatch({ type: 'SIGN_OUT' });
        }
      },
      []
    ),

    updateProfile: useCallback(
      async (profileData) => {
        try {
          setError(null);
          const response = await APIService.updateProfile(profileData);
          const updatedUser = { ...state.user, ...response.user };
          await StorageService.saveUser(updatedUser);

          dispatch({
            type: 'UPDATE_USER',
            user: updatedUser,
          });

          return { success: true, data: updatedUser };
        } catch (err) {
          const errorMessage = err.data?.message || 'Profile update failed.';
          setError(errorMessage);
          return { success: false, error: errorMessage };
        }
      },
      [state.user]
    ),

    updatePassword: useCallback(
      async (currentPassword, newPassword, confirmPassword) => {
        try {
          setError(null);
          await APIService.updatePassword(currentPassword, newPassword, confirmPassword);
          return { success: true };
        } catch (err) {
          const errorMessage = err.data?.message || 'Password update failed.';
          setError(errorMessage);
          return { success: false, error: errorMessage };
        }
      },
      []
    ),

    resendVerificationEmail: useCallback(
      async (email) => {
        try {
          setError(null);
          const response = await APIService.resendVerificationEmail(email);
          return { success: true, data: response };
        } catch (err) {
          const errorMessage = err.data?.message || 'Failed to resend verification email.';
          setError(errorMessage);
          return { success: false, error: errorMessage };
        }
      },
      []
    ),

    forgotPassword: useCallback(
      async (email) => {
        try {
          setError(null);
          const response = await APIService.forgotPassword(email);
          return { success: true, data: response };
        } catch (err) {
          const errorMessage = err.data?.message || 'Failed to process password reset.';
          setError(errorMessage);
          return { success: false, error: errorMessage };
        }
      },
      []
    ),

    clearError: useCallback(() => {
      setError(null);
    }, []),
  };

  return <AuthContext.Provider value={{ ...authContext, error }}>{children}</AuthContext.Provider>;
};

export function useAuth() {
  const context = React.useContext(AuthContext);
  if (!context) {
    throw new Error('useAuth must be used within an AuthProvider');
  }
  return context;
}
