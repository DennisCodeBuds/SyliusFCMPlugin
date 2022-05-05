export class Notification {
  init() {
    this._handleForm();
  }

  _handleForm() {
    const submitButton = document.getElementById('notificationSubmit');
    this.typeSelector = document.getElementById('notification_targetType');
    console.log(this.typeSelector);
    this.targetField = document.getElementById('notification_target');
    submitButton.addEventListener('click', () => this.submitForm());
    this.typeSelector.addEventListener('change', () => this.submitTypeChange());
  }

  submitForm() {
    const Form = document.getElementById('notificationForm');
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

  submitTypeChange() {
    console.log(this.typeSelector, 'trigerred');
    const Form = document.getElementById('notificationForm');
    const XHR = new XMLHttpRequest();
    const FD = new FormData();

    FD.append(this.typeSelector.getAttribute('name'), this.typeSelector.value);

    // Define what happens on successful data submission
    XHR.addEventListener('load', (event) => {
      const form = this.htmlToElements(event.target.response);
      console.log(form);
      this.targetField.innerHTML = form.getElementById('notification_target');
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

  htmlToElements(html) {
    let template = document.createElement('template');
    template.innerHTML = html;
    return template.content.childNodes;
  }
}

export default Notification;
