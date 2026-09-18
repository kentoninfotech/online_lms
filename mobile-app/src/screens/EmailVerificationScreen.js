/**
 * Email Verification Screen
 * Displayed after registration to verify email before login
 */

import React, { useState, useEffect } from 'react';
import {
  View,
  ScrollView,
  Text,
  StyleSheet,
  TouchableOpacity,
  ActivityIndicator,
  Alert,
} from 'react-native';
import { useAuth } from '../context/AuthContext';

export default function EmailVerificationScreen({ navigation, route }) {
  const { email, user } = route.params || {};
  const [loading, setLoading] = useState(false);
  const [emailSending, setEmailSending] = useState(false);
  const [verificationSent, setVerificationSent] = useState(false);
  const [error, setError] = useState(route.params?.emailError || null);
  const [successMessage, setSuccessMessage] = useState(null);
  const { resendVerificationEmail } = useAuth();

  const handleResendEmail = async () => {
    if (!email) {
      Alert.alert('Error', 'Email address not found');
      return;
    }

    setEmailSending(true);
    setError(null);
    setSuccessMessage(null);

    const result = await resendVerificationEmail(email);

    setEmailSending(false);

    if (result.success) {
      setVerificationSent(true);
      setSuccessMessage('Verification email sent successfully! Check your inbox.');
      // Clear success message after 5 seconds
      setTimeout(() => setSuccessMessage(null), 5000);
    } else {
      setError(result.error || 'Failed to send verification email');
    }
  };

  const handleOpenEmail = () => {
    // Navigate to login screen
    navigation.replace('Login');
  };

  return (
    <ScrollView style={styles.container} contentContainerStyle={styles.contentContainer}>
      {/* Header */}
      <View style={styles.header}>
        <Text style={styles.icon}>✉️</Text>
        <Text style={styles.title}>Verify Your Email</Text>
        <Text style={styles.subtitle}>
          {error ? 'Email sending failed - please try again' : 'One last step to complete your registration'}
        </Text>
      </View>

      {/* Main Content */}
      <View style={styles.content}>
        {/* Error Message */}
        {error && (
          <View style={styles.errorContainer}>
            <Text style={styles.errorIcon}>⚠️</Text>
            <Text style={styles.errorText}>{error}</Text>
            <Text style={styles.errorHint}>Click "Resend Verification Email" to try again</Text>
          </View>
        )}

        {/* Success Message */}
        {successMessage && (
          <View style={styles.successContainer}>
            <Text style={styles.successIcon}>✅</Text>
            <Text style={styles.successText}>{successMessage}</Text>
          </View>
        )}

        {/* Email Info */}
        <View style={styles.emailSection}>
          <Text style={styles.sectionLabel}>Verification link sent to:</Text>
          <View style={styles.emailBox}>
            <Text style={styles.emailText}>{email || 'your email'}</Text>
          </View>
        </View>

        {/* Instructions */}
        <View style={styles.instructionsSection}>
          <Text style={styles.instructionsTitle}>What to do next?</Text>
          <View style={styles.instructionsList}>
            <View style={styles.instructionItem}>
              <Text style={styles.stepNumber}>1</Text>
              <Text style={styles.instructionText}>
                Check your email inbox (and spam folder) for a message from us
              </Text>
            </View>
            <View style={styles.instructionItem}>
              <Text style={styles.stepNumber}>2</Text>
              <Text style={styles.instructionText}>
                Click the verification link in the email
              </Text>
            </View>
            <View style={styles.instructionItem}>
              <Text style={styles.stepNumber}>3</Text>
              <Text style={styles.instructionText}>
                Return here and log in to access your dashboard
              </Text>
            </View>
          </View>
        </View>

        {/* Resend Email Button */}
        <View style={styles.buttonsSection}>
          <TouchableOpacity
            style={[styles.resendButton, emailSending && styles.disabledButton]}
            onPress={handleResendEmail}
            disabled={emailSending}
          >
            {emailSending ? (
              <ActivityIndicator color="#fff" />
            ) : (
              <>
                <Text style={styles.resendButtonIcon}>🔄 </Text>
                <Text style={styles.resendButtonText}>Resend Verification Email</Text>
              </>
            )}
          </TouchableOpacity>

          {/* Help Text */}
          <View style={styles.helpBox}>
            <Text style={styles.helpTitle}>Didn't receive the email?</Text>
            <Text style={styles.helpText}>
              Check your spam folder or click the button above to request a new verification link. It may take a few minutes to arrive.
            </Text>
          </View>

          {/* Login Button */}
          <TouchableOpacity
            style={styles.loginButton}
            onPress={handleOpenEmail}
            disabled={emailSending}
          >
            <Text style={styles.loginButtonIcon}>← </Text>
            <Text style={styles.loginButtonText}>Return to Login</Text>
          </TouchableOpacity>
        </View>
      </View>

      {/* Footer Info */}
      <View style={styles.footer}>
        <Text style={styles.footerText}>
          Once verified, you can log in and start learning!
        </Text>
      </View>
    </ScrollView>
  );
}

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: '#f5f5f5',
  },
  contentContainer: {
    flexGrow: 1,
    padding: 20,
  },
  header: {
    alignItems: 'center',
    marginBottom: 30,
    marginTop: 20,
  },
  icon: {
    fontSize: 60,
    marginBottom: 16,
  },
  title: {
    fontSize: 28,
    fontWeight: '700',
    color: '#333',
    marginBottom: 8,
    textAlign: 'center',
  },
  subtitle: {
    fontSize: 16,
    color: '#666',
    textAlign: 'center',
  },
  content: {
    flex: 1,
  },
  errorContainer: {
    backgroundColor: '#fee',
    borderRadius: 8,
    padding: 16,
    marginBottom: 16,
    borderLeftWidth: 4,
    borderLeftColor: '#c33',
  },
  errorIcon: {
    fontSize: 24,
    marginBottom: 8,
  },
  errorText: {
    color: '#c33',
    fontSize: 14,
    lineHeight: 20,
  },
  errorHint: {
    color: '#c33',
    fontSize: 12,
    marginTop: 8,
    fontStyle: 'italic',
    opacity: 0.8,
  },
  successContainer: {
    backgroundColor: '#efe',
    borderRadius: 8,
    padding: 16,
    marginBottom: 16,
    borderLeftWidth: 4,
    borderLeftColor: '#3c3',
  },
  successIcon: {
    fontSize: 24,
    marginBottom: 8,
  },
  successText: {
    color: '#3c3',
    fontSize: 14,
    lineHeight: 20,
    fontWeight: '600',
  },
  emailSection: {
    backgroundColor: '#fff',
    borderRadius: 8,
    padding: 16,
    marginBottom: 20,
  },
  sectionLabel: {
    fontSize: 14,
    color: '#666',
    marginBottom: 8,
    fontWeight: '600',
  },
  emailBox: {
    backgroundColor: '#f0f0f0',
    borderRadius: 6,
    padding: 12,
    borderLeftWidth: 3,
    borderLeftColor: '#007AFF',
  },
  emailText: {
    fontSize: 14,
    color: '#007AFF',
    fontFamily: 'Courier New',
    fontWeight: '500',
  },
  instructionsSection: {
    backgroundColor: '#fff',
    borderRadius: 8,
    padding: 16,
    marginBottom: 20,
  },
  instructionsTitle: {
    fontSize: 16,
    fontWeight: '700',
    color: '#333',
    marginBottom: 12,
  },
  instructionsList: {
    gap: 12,
  },
  instructionItem: {
    flexDirection: 'row',
    gap: 12,
  },
  stepNumber: {
    fontSize: 16,
    fontWeight: '700',
    width: 24,
    height: 24,
    borderRadius: 12,
    backgroundColor: '#007AFF',
    color: '#fff',
    textAlign: 'center',
    textAlignVertical: 'center',
  },
  instructionText: {
    fontSize: 14,
    color: '#555',
    lineHeight: 20,
    flex: 1,
  },
  buttonsSection: {
    gap: 16,
  },
  resendButton: {
    backgroundColor: '#007AFF',
    borderRadius: 8,
    paddingVertical: 14,
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'center',
    gap: 8,
  },
  disabledButton: {
    opacity: 0.6,
  },
  resendButtonIcon: {
    fontSize: 18,
  },
  resendButtonText: {
    color: '#fff',
    fontSize: 16,
    fontWeight: '600',
  },
  helpBox: {
    backgroundColor: '#f0f7ff',
    borderRadius: 8,
    padding: 14,
    borderLeftWidth: 3,
    borderLeftColor: '#007AFF',
  },
  helpTitle: {
    fontSize: 14,
    fontWeight: '700',
    color: '#333',
    marginBottom: 6,
  },
  helpText: {
    fontSize: 13,
    color: '#666',
    lineHeight: 18,
  },
  loginButton: {
    backgroundColor: '#fff',
    borderRadius: 8,
    borderWidth: 1.5,
    borderColor: '#ccc',
    paddingVertical: 14,
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'center',
    gap: 8,
  },
  loginButtonIcon: {
    fontSize: 18,
  },
  loginButtonText: {
    color: '#333',
    fontSize: 16,
    fontWeight: '600',
  },
  footer: {
    marginTop: 20,
    paddingTop: 16,
    borderTopWidth: 1,
    borderTopColor: '#eee',
  },
  footerText: {
    fontSize: 14,
    color: '#666',
    textAlign: 'center',
    fontStyle: 'italic',
  },
});
