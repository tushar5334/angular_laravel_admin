import { Component, OnInit } from '@angular/core';
import {
  Validators,
  FormGroup,
  FormBuilder,
  FormControl,
  AbstractControl,
} from '@angular/forms';
import { Router } from '@angular/router';
import { ILogin } from '../interfaces/login';
import { AuthService } from '../services/auth.service';
import { SnackbarService } from '../services/snackbar.service';
@Component({
  selector: 'app-login',
  templateUrl: './login.component.html',
  styleUrls: ['./login.component.scss'],
})
export class LoginComponent implements OnInit {
  submitted: boolean = false;
  serverErrors: { [key: string]: string } = {};
  loginForm = this.fb.group({
    email: ['', [Validators.required, Validators.email]],
    password: ['', [Validators.required, Validators.minLength(5)]],
  });

  constructor(
    private fb: FormBuilder, //private customValidator: CustomValidationService
    private authService: AuthService,
    private router: Router,
    private snackbarService: SnackbarService
  ) {}

  ngOnInit(): void {}

  get loginFormControl(): { [key: string]: AbstractControl } {
    return this.loginForm.controls;
  }

  onLogin(): boolean | void {
    //this.snackbarService.success('Sucessfully logged in!!!😃');
    this.serverErrors = {};
    this.submitted = true;
    if (this.loginForm.invalid) {
      return true;
    }
    let login: ILogin = this.loginForm.value;
    this.authService.login(login).subscribe({
      next: (result: any) => {
        this.submitted = false;
        this.authService.setAuthData(result.data.token, result.data.user, true);
        this.snackbarService.success(result.message);
        this.router.navigate(['/admin']);
      },
      error: (error: any) => {
        this.submitted = false;
        if (error.status === 422 && error.error && error.error.errors) {
          /* for(var key in serverResponse["errors"]) { 
            this.serverErrors[key] = serverResponse["errors"][key]
            this.form.controls[key].setErrors({serverValidationError: true});
                                  OR
            this.loginFormControl[key].setValidators(
              (f) => <any>{ serverValidationError: true }
            ); 
            this.loginFormControl[key].updateValueAndValidity();
          } */
          for (let key in error.error.errors) {
            this.loginFormControl[key].setErrors({
              serverValidationError: true,
            });
            this.serverErrors[key] = error.error.errors[key][0] as string;
          }
        } else if (error.status === 422 && error.message) {
          this.snackbarService.error(error.error.message);
        } else {
          this.snackbarService.error(
            'Something went wrong, Please try again later.'
          );
        }
        this.authService.authStatus(false);
      },
    });
  }

  onReset() {
    this.submitted = false;
    this.loginForm.reset();
  }
}
