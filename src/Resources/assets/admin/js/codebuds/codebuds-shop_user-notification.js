export class ShopUserNotification {
  init() {
    this._handleForm();
  }

  _handleForm() {
    this.submitButton = document.getElementById('notificationSubmit');
    this.submitButton.disabled = false;
    this.submitButton.addEventListener('click', () => this.submitForm());
  }

  submitForm() {
    const Form = document.getElementById('shopUserNotificationForm');
    const XHR = new XMLHttpRequest();
    const FD = new FormData(Form);

    // Define what happens on successful data submission
    XHR.addEventListener('load', (event) => {
      if (XHR.status === 200) {
        const url = JSON.parse(event.target.responseText).url;
        window.location.replace(url);
      } else {
        Form.innerHTML = event.target.responseText;
        this._handleForm();
      }
    });

    // Define what happens in case of error
    XHR.addEventListener('error', (event) => {
      alert('Oops! Something went wrong.');
    });

    // Set up our request
    XHR.open('POST', Form.action, false);

    // The data sent is what the user provided in the form

    XHR.send(FD);
  }

  disableSubmitButton() {
    this.submitButton.disabled = true;
  }
}

export default ShopUserNotification;
