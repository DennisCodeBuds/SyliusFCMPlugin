export class HandleCredentialsUpload {
  constructor(config = { textField: 'data-cb-fcm-text', fileField: 'data-cb-fcm-file' }) {
    this.config = config;
    this.textField = document.querySelector(`[${config.textField}]`);
    this.fileField = document.querySelector(`[${config.fileField}]`);
  }

  init() {
    if (typeof this.config !== 'object') {
      throw new Error('CodeBuds FCM Plugin - HandleCredentialsUpload class config is not a valid object');
    }

    this._handleFields();
    this._handleUpload();
  }

  _handleFields() {
    this.textField = document.querySelector(`[${this.config.textField}]`);
    this.fileField = document.querySelector(`[${this.config.fileField}]`);
    this._handleTextField();
    this._handleFileField();
  }

  resetForm() {
    this._handleFields();
    this._handleUpload();
  }

  _handleTextField() {
    this.textField.addEventListener('click', () => {
      this.fileField.click();
    });
  }

  _handleFileField() {
    this.fileField.addEventListener('change', (e) => {
      this.textField.value = e.target.files[0].name;
    });
  }

  _handleUpload() {
    const submitButton = document.getElementById('credentialsUploadSubmit');
    submitButton.addEventListener('click', () => this.submitForm());
  }

  submitForm() {
    const uploadForm = document.getElementById('credentialsUploadForm');
    const XHR = new XMLHttpRequest();
    const FD = new FormData(uploadForm);

    // Define what happens on successful data submission
    XHR.addEventListener('load', (event) => {
      const response = JSON.parse(event.target.responseText);
      uploadForm.innerHTML = response.form;
      this.resetForm();
    });

    // Define what happens in case of error
    XHR.addEventListener('error', (event) => {
      alert('Oops! Something went wrong.');
    });

    // Set up our request
    XHR.open('POST', uploadForm.action, false);

    // The data sent is what the user provided in the form
    XHR.send(FD);
  }
}

export default HandleCredentialsUpload;
