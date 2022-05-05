import {
  HandleCredentialsUpload,
  Notification,
  ProductNotification,
  ShopUserNotification,
  TestNotification,
  PromotionalNotification,
} from './codebuds';

if (document.querySelector('[data-cb-target="fcm-import"]')) {
  new HandleCredentialsUpload().init();
}

if (document.getElementById('testNotificationForm')) {
  new TestNotification().init();
}

if (document.getElementById('notificationForm')) {
  new Notification().init();
}

if (document.getElementById('shopUserNotificationForm')) {
  new ShopUserNotification().init();
}

if (document.getElementById('productNotificationForm')) {
  new ProductNotification().init();
}

if (document.getElementById('promotionalNotificationForm')) {
  new PromotionalNotification().init();
}

