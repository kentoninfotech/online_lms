/**
 * Root App Layout
 * Main entry point with Auth Context Provider
 */

import React from 'react';
import { AuthProvider } from '../src/context/AuthContext';
import { RootNavigator } from '../src/navigation/RootNavigator';
import { GestureHandlerRootView } from 'react-native-gesture-handler';

export default function RootLayout() {
  return (
    <GestureHandlerRootView style={{ flex: 1 }}>
      <AuthProvider>
        <RootNavigator />
      </AuthProvider>
    </GestureHandlerRootView>
  );
}
