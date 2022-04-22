export class TestNotification {
  init() {
    this._handleForm();
  }

  _handleForm() {
    const submitButton = document.getElementById('testNotificationSubmit');
    submitButton.addEventListener('click', () => this.submitForm());
  }

  submitForm() {
    const Form = document.getElementById('testNotificationForm');
    const XHR = new XMLHttpRequest();
    const FD = new FormData(Form);

    // Define what happens on successful data submission
    XHR.addEventListener('load', (event) => {
      const response = JSON.parse(event.target.responseText);
      Form.innerHTML = response.form;
      this._handleForm();
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
}

export default TestNotification;
