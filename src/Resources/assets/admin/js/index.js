import { HandleCredentialsUpload, TestNotification } from './codebuds';

if (document.querySelector('[data-cb-target="fcm-import"]')) {
  new HandleCredentialsUpload().init();
}

if (document.getElementById('testNotificationForm')) {
  new TestNotification().init();
}
