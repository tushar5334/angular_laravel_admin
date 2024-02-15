import { ValidatorFn, AbstractControl, ValidationErrors } from '@angular/forms';
import { FormGroup } from '@angular/forms';

export function urlValidator(
  control: AbstractControl
): ValidationErrors | null {
  if (!control.value.startsWith('https') || !control.value.includes('.me')) {
    return { urlValid: true };
  }
  return null;
}

export function patternValidator(): ValidatorFn {
  return (control: AbstractControl): any => {
    if (!control.value) {
      return null;
    }
    const regex = new RegExp('^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9]).{5,}$');
    const valid = regex.test(control.value);
    return valid ? null : { invalidPassword: true };
  };
}

export function MatchPassword(
  password: string,
  confirmPassword: string
): ValidationErrors | null {
  return (formGroup: FormGroup) => {
    const passwordControl = formGroup.controls[password];
    const confirmPasswordControl = formGroup.controls[confirmPassword];

    if (!passwordControl || !confirmPasswordControl) {
      return null;
    }

    if (
      confirmPasswordControl.errors &&
      !confirmPasswordControl.errors['passwordMismatch']
    ) {
      return null;
    }

    if (passwordControl.value !== confirmPasswordControl.value) {
      confirmPasswordControl.setErrors({ passwordMismatch: true });
      return true;
    } else {
      confirmPasswordControl.setErrors(null);
      return null;
    }
  };
}

export function userNameValidator(
  userControl: AbstractControl
): ValidationErrors | null {
  /* return new Promise((resolve) => {
    setTimeout(() => {
      if (validateUserName(userControl.value)) {
        resolve({ userNameNotAvailable: true });
      } else {
        resolve(null);
      }
    }, 1000);
  }); */
  if (validateUserName(userControl.value)) {
    return { userNameNotAvailable: true };
  } else {
    return null;
  }
}

export function validateUserName(userName: string) {
  /* A static array is used to validate the availability of user names.
   *  Ideally it should be a service call to the server to search the value from a database.
   */

  const UserList = ['ankit', 'admin', 'user', 'superuser'];
  return UserList.indexOf(userName) > -1;
}
