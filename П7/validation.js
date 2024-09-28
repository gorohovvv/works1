function validateForm() {
    var nameInput = document.getElementById('name');
    var nameError = document.getElementById('nameError');
    var name = nameInput.value.trim();

    var emailInput = document.getElementById('email');
    var emailError = document.getElementById('emailError');
    var email = emailInput.value.trim();

    var passwordInput = document.getElementById('password');
    var passwordError = document.getElementById('passwordError');
    var password = passwordInput.value;

    var passwordConfirmInput = document.getElementById('password_c');
    var passwordConfirmError = document.getElementById('password_cError');
    var passwordConfirm = passwordConfirmInput.value;

    var phoneInput = document.getElementById('phone');
    var phoneError = document.getElementById('phoneError');
    var phone = phoneInput.value.trim();

    var dateInput = document.getElementById('date');
    var dateError = document.getElementById('dateError');
    var dateOfBirth = dateInput.value ? new Date(dateInput.value) : null;
    var currentDate = new Date();
    var minDate = new Date(currentDate.getFullYear() - 111, currentDate.getMonth(), currentDate.getDate());

    var genderInput = document.querySelector('input[name="gender"]:checked');
    var genderError = document.getElementById('genderError');
    var gender = genderInput ? genderInput.value : null;

    var imageInput = document.getElementById('image');
    var imageError = document.getElementById('imageError');
    var image = imageInput.files[0];

    var error = false;

    sessionStorage.clear();

    function displayError(element, errorMessage) {
        element.textContent = errorMessage;
        error = true;
    }

    nameError.textContent = '';
    emailError.textContent = '';
    passwordError.textContent = '';
    passwordConfirmError.textContent = '';
    phoneError.textContent = '';
    dateError.textContent = '';
    genderError.textContent = '';
    imageError.textContent = '';

    if (name === '') {
        displayError(nameError, 'Имя не может быть пустым');
    } else if (/\d/.test(name)) {
        displayError(nameError, 'Имя не может содержать цифры');
    }

    var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (email === '') {
        displayError(emailError, 'Email не может быть пустым');
    }
    else if (!emailRegex.test(email)) {
        displayError(emailError, 'Неверный формат электронной почты');
    }

    var currentMonth = currentDate.toLocaleString('en', {
        month: 'long'
    }).toLowerCase();
    var passwordRegex = new RegExp(
        "(?=.{8,})" + 
        "(?!.*(.)\\1{1})" + 
        "(?=.*\\W.*\\W)" + 
        "(?=.*[" + currentMonth + "])" + 
        "(?=.*[a-zA-Zа-яА-Я])" + 
        "(?=.*[a-zA-Z])" +
        "(?=.*[а-яА-Я])" + 
        "(?!.*\\W{3,})" 
    );
    

    if (password === '') {
        displayError(passwordError, 'Пароль не может быть пустым');
    }
    else if (!passwordRegex.test(password)) {
        displayError(passwordError,
            'Пароль должен содержать не менее 8 символов, не содержать двух одинаковых символов подряд, содержать как минимум 2 специальных символа, включать название текущего месяца на английском языке и не содержать более 2 подряд идущих специальных символов, должны быть русские и английские буквы');
    }

    if (passwordConfirm === '') {
        displayError(passwordConfirmError, 'Пароль не может быть пустым');
    }
    else if (password !== passwordConfirm) {
        displayError(passwordConfirmError, 'Пароли не совпадают');
    }

    if (phone === '') {
        displayError(phoneError, 'Номер телефона не может быть пустым');
    }

    if (!dateOfBirth) {
        displayError(dateError, 'Дата не может быть пустой');
    }
    else if (!dateOfBirth || dateOfBirth > currentDate || dateOfBirth < minDate) {
        displayError(dateError, 'Неверная дата рождения');
    }

    if (!gender) {
        displayError(genderError, 'Пожалуйста, выберите свой пол');
    }

    if (!image) {
        displayError(imageError, 'Пожалуйста, выберите изображение');
    } else {
        var imageName = image.name;
        var imageSize = image.size;
        var imageType = image.type;

        if (imageName.length > 15) {
            displayError(imageError, 'Имя изображения не должно превышать 15 символов');
        }

        if (imageType !== 'image/png') {
            displayError(imageError, 'Изображение должно быть в формате PNG');
        }

        if (imageSize < 1024 || imageSize > 1024 * 1024 * 10) {
            displayError(imageError, 'Размер изображения должен быть от 1 КБ до 10 МБ');
        }
    }

    return !error;
}

document.addEventListener('DOMContentLoaded', function () {
    var phoneInput = document.getElementById('phone');
    var phoneMask = new Inputmask("+7 (999) 999-99-99", {
        "placeholder": "_"
    });
    phoneMask.mask(phoneInput);
});