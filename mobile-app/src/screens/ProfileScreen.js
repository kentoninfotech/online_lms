/**
 * Profile Screen
 * User profile management and settings
 */

import React, { useState, useEffect } from 'react';
import {
  View,
  ScrollView,
  TouchableOpacity,
  Text,
  StyleSheet,
  TextInput,
  ActivityIndicator,
  Alert,
  Image,
} from 'react-native';
import { useAuth } from '../context/AuthContext';
import APIService from '../services/api';

export default function ProfileScreen({ navigation }) {
  const { state, updateProfile, updatePassword, signOut } = useAuth();
  const [tab, setTab] = useState('profile'); // profile, password, settings
  const [loading, setLoading] = useState(false);
  const [profile, setProfile] = useState(state.user || {});
  const [passwordData, setPasswordData] = useState({
    currentPassword: '',
    newPassword: '',
    confirmPassword: '',
  });

  const handleUpdateProfile = async () => {
    if (!profile.name || !profile.email) {
      Alert.alert('Required Fields', 'Name and email are required');
      return;
    }

    setLoading(true);
    const result = await updateProfile({
      name: profile.name,
      email: profile.email,
    });
    setLoading(false);

    if (result.success) {
      Alert.alert('Success', 'Profile updated successfully');
    } else {
      Alert.alert('Error', result.error);
    }
  };

  const handleUpdatePassword = async () => {
    if (!passwordData.currentPassword || !passwordData.newPassword) {
      Alert.alert('Required Fields', 'Please fill all fields');
      return;
    }

    if (passwordData.newPassword !== passwordData.confirmPassword) {
      Alert.alert('Mismatch', 'New passwords do not match');
      return;
    }

    setLoading(true);
    const result = await updatePassword(
      passwordData.currentPassword,
      passwordData.newPassword,
      passwordData.confirmPassword
    );
    setLoading(false);

    if (result.success) {
      Alert.alert('Success', 'Password updated successfully');
      setPasswordData({
        currentPassword: '',
        newPassword: '',
        confirmPassword: '',
      });
    } else {
      Alert.alert('Error', result.error);
    }
  };

  const handleLogout = async () => {
    Alert.alert('Logout', 'Are you sure you want to logout?', [
      { text: 'Cancel', style: 'cancel' },
      {
        text: 'Logout',
        style: 'destructive',
        onPress: async () => {
          await signOut();
        },
      },
    ]);
  };

  return (
    <ScrollView style={styles.container}>
      {/* Profile Header */}
      <View style={styles.header}>
        <View style={styles.avatarContainer}>
          {profile.avatar ? (
            <Image
              source={{ uri: profile.avatar }}
              style={styles.avatar}
            />
          ) : (
            <View style={styles.avatarPlaceholder}>
              <Text style={styles.avatarInitial}>
                {profile.name?.charAt(0).toUpperCase()}
              </Text>
            </View>
          )}
        </View>
        <Text style={styles.userName}>{profile.name}</Text>
        <Text style={styles.userRole}>
          {profile.role === 'tutor' ? '👨‍🏫 Tutor' : '👨‍🎓 Learner'}
        </Text>
      </View>

      {/* Tabs */}
      <View style={styles.tabsContainer}>
        <TouchableOpacity
          style={[styles.tab, tab === 'profile' && styles.activeTab]}
          onPress={() => setTab('profile')}
        >
          <Text style={[styles.tabText, tab === 'profile' && styles.activeTabText]}>
            Profile
          </Text>
        </TouchableOpacity>
        <TouchableOpacity
          style={[styles.tab, tab === 'password' && styles.activeTab]}
          onPress={() => setTab('password')}
        >
          <Text style={[styles.tabText, tab === 'password' && styles.activeTabText]}>
            Password
          </Text>
        </TouchableOpacity>
        <TouchableOpacity
          style={[styles.tab, tab === 'settings' && styles.activeTab]}
          onPress={() => setTab('settings')}
        >
          <Text style={[styles.tabText, tab === 'settings' && styles.activeTabText]}>
            Settings
          </Text>
        </TouchableOpacity>
      </View>

      {/* Profile Tab Content */}
      {tab === 'profile' && (
        <View style={styles.content}>
          <View style={styles.section}>
            <Text style={styles.label}>Full Name</Text>
            <TextInput
              style={styles.input}
              value={profile.name}
              onChangeText={value => setProfile({ ...profile, name: value })}
              placeholder="Enter your name"
              editable={!loading}
            />
          </View>

          <View style={styles.section}>
            <Text style={styles.label}>Email Address</Text>
            <TextInput
              style={styles.input}
              value={profile.email}
              onChangeText={value => setProfile({ ...profile, email: value })}
              placeholder="Enter your email"
              keyboardType="email-address"
              editable={!loading}
            />
          </View>

          {profile.phone && (
            <View style={styles.section}>
              <Text style={styles.label}>Phone</Text>
              <TextInput
                style={styles.input}
                value={profile.phone}
                onChangeText={value => setProfile({ ...profile, phone: value })}
                placeholder="Enter your phone"
                editable={!loading}
              />
            </View>
          )}

          <TouchableOpacity
            style={[styles.button, loading && styles.disabledButton]}
            onPress={handleUpdateProfile}
            disabled={loading}
          >
            {loading ? (
              <ActivityIndicator color="#fff" />
            ) : (
              <Text style={styles.buttonText}>Save Changes</Text>
            )}
          </TouchableOpacity>
        </View>
      )}

      {/* Password Tab Content */}
      {tab === 'password' && (
        <View style={styles.content}>
          <View style={styles.section}>
            <Text style={styles.label}>Current Password</Text>
            <TextInput
              style={styles.input}
              value={passwordData.currentPassword}
              onChangeText={value =>
                setPasswordData({ ...passwordData, currentPassword: value })
              }
              placeholder="Enter current password"
              secureTextEntry
              editable={!loading}
            />
          </View>

          <View style={styles.section}>
            <Text style={styles.label}>New Password</Text>
            <TextInput
              style={styles.input}
              value={passwordData.newPassword}
              onChangeText={value =>
                setPasswordData({ ...passwordData, newPassword: value })
              }
              placeholder="Enter new password (min. 8 characters)"
              secureTextEntry
              editable={!loading}
            />
          </View>

          <View style={styles.section}>
            <Text style={styles.label}>Confirm Password</Text>
            <TextInput
              style={styles.input}
              value={passwordData.confirmPassword}
              onChangeText={value =>
                setPasswordData({ ...passwordData, confirmPassword: value })
              }
              placeholder="Confirm new password"
              secureTextEntry
              editable={!loading}
            />
          </View>

          <TouchableOpacity
            style={[styles.button, loading && styles.disabledButton]}
            onPress={handleUpdatePassword}
            disabled={loading}
          >
            {loading ? (
              <ActivityIndicator color="#fff" />
            ) : (
              <Text style={styles.buttonText}>Update Password</Text>
            )}
          </TouchableOpacity>
        </View>
      )}

      {/* Settings Tab Content */}
      {tab === 'settings' && (
        <View style={styles.content}>
          <View style={styles.settingsSection}>
            <Text style={styles.sectionTitle}>Account Information</Text>
            <View style={styles.settingItem}>
              <Text style={styles.settingLabel}>Email</Text>
              <Text style={styles.settingValue}>{profile.email}</Text>
            </View>
            <View style={styles.settingItem}>
              <Text style={styles.settingLabel}>Role</Text>
              <Text style={styles.settingValue}>
                {profile.role === 'tutor' ? 'Tutor' : 'Learner'}
              </Text>
            </View>
            {profile.created_at && (
              <View style={styles.settingItem}>
                <Text style={styles.settingLabel}>Member Since</Text>
                <Text style={styles.settingValue}>
                  {new Date(profile.created_at).toLocaleDateString()}
                </Text>
              </View>
            )}
          </View>

          <View style={styles.settingsSection}>
            <Text style={styles.sectionTitle}>Support</Text>
            <TouchableOpacity style={styles.settingButton}>
              <Text style={styles.settingButtonText}>Help & Support</Text>
              <Text style={styles.settingArrow}>→</Text>
            </TouchableOpacity>
            <TouchableOpacity style={styles.settingButton}>
              <Text style={styles.settingButtonText}>Terms & Conditions</Text>
              <Text style={styles.settingArrow}>→</Text>
            </TouchableOpacity>
            <TouchableOpacity style={styles.settingButton}>
              <Text style={styles.settingButtonText}>Privacy Policy</Text>
              <Text style={styles.settingArrow}>→</Text>
            </TouchableOpacity>
          </View>

          <TouchableOpacity
            style={styles.logoutButton}
            onPress={handleLogout}
          >
            <Text style={styles.logoutButtonText}>Logout</Text>
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
  header: {
    backgroundColor: '#fff',
    paddingVertical: 24,
    alignItems: 'center',
    borderBottomWidth: 1,
    borderBottomColor: '#e0e0e0',
  },
  avatarContainer: {
    marginBottom: 12,
  },
  avatar: {
    width: 80,
    height: 80,
    borderRadius: 40,
    backgroundColor: '#e0e0e0',
  },
  avatarPlaceholder: {
    width: 80,
    height: 80,
    borderRadius: 40,
    backgroundColor: '#007AFF',
    justifyContent: 'center',
    alignItems: 'center',
  },
  avatarInitial: {
    fontSize: 32,
    fontWeight: 'bold',
    color: '#fff',
  },
  userName: {
    fontSize: 20,
    fontWeight: 'bold',
    color: '#333',
    marginBottom: 4,
  },
  userRole: {
    fontSize: 14,
    color: '#666',
  },
  tabsContainer: {
    flexDirection: 'row',
    backgroundColor: '#fff',
    borderBottomWidth: 1,
    borderBottomColor: '#e0e0e0',
  },
  tab: {
    flex: 1,
    paddingVertical: 12,
    alignItems: 'center',
    borderBottomWidth: 3,
    borderBottomColor: 'transparent',
  },
  activeTab: {
    borderBottomColor: '#007AFF',
  },
  tabText: {
    fontSize: 14,
    color: '#666',
    fontWeight: '500',
  },
  activeTabText: {
    color: '#007AFF',
    fontWeight: '600',
  },
  content: {
    padding: 16,
    paddingBottom: 32,
  },
  section: {
    marginBottom: 16,
  },
  label: {
    fontSize: 14,
    fontWeight: '600',
    color: '#333',
    marginBottom: 8,
  },
  input: {
    backgroundColor: '#fff',
    borderWidth: 1,
    borderColor: '#ddd',
    borderRadius: 8,
    paddingHorizontal: 12,
    paddingVertical: 12,
    fontSize: 14,
    color: '#333',
  },
  button: {
    backgroundColor: '#007AFF',
    paddingVertical: 14,
    borderRadius: 8,
    alignItems: 'center',
    marginTop: 8,
  },
  disabledButton: {
    opacity: 0.6,
  },
  buttonText: {
    color: '#fff',
    fontSize: 16,
    fontWeight: '600',
  },
  settingsSection: {
    backgroundColor: '#fff',
    borderRadius: 8,
    marginBottom: 16,
    overflow: 'hidden',
  },
  sectionTitle: {
    fontSize: 14,
    fontWeight: '600',
    color: '#333',
    paddingHorizontal: 16,
    paddingVertical: 12,
    borderBottomWidth: 1,
    borderBottomColor: '#e0e0e0',
  },
  settingItem: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    paddingHorizontal: 16,
    paddingVertical: 12,
    borderBottomWidth: 1,
    borderBottomColor: '#e0e0e0',
  },
  settingLabel: {
    fontSize: 14,
    color: '#666',
    fontWeight: '500',
  },
  settingValue: {
    fontSize: 14,
    color: '#333',
    fontWeight: '600',
  },
  settingButton: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    paddingHorizontal: 16,
    paddingVertical: 12,
    borderBottomWidth: 1,
    borderBottomColor: '#e0e0e0',
  },
  settingButtonText: {
    fontSize: 14,
    color: '#333',
    flex: 1,
  },
  settingArrow: {
    fontSize: 16,
    color: '#999',
  },
  logoutButton: {
    backgroundColor: '#dc3545',
    paddingVertical: 14,
    borderRadius: 8,
    alignItems: 'center',
    marginTop: 16,
  },
  logoutButtonText: {
    color: '#fff',
    fontSize: 16,
    fontWeight: '600',
  },
});
