import { Component, OnInit } from '@angular/core';
import { Validators, FormBuilder, AbstractControl } from '@angular/forms';
import { Router } from '@angular/router';
import { IRegister } from '../interfaces/register';
import { AuthService } from '../services/auth.service';
import { SnackbarService } from '../services/snackbar.service';
import {
  patternValidator,
  MatchPassword,
} from '../validators/custom.validators';
@Component({
  selector: 'app-register',
  templateUrl: './register.component.html',
  styleUrls: ['./register.component.scss'],
})
export class RegisterComponent implements OnInit {
  submitted: boolean = false;
  serverErrors: { [key: string]: string } = {};
  registerForm = this.fb.group(
    {
      name: ['', [Validators.required]],
      email: ['', [Validators.required, Validators.email]],
      password: [
        '',
        [
          Validators.required,
          // Validators.minLength(5),
          patternValidator(),
        ],
      ],
      password_confirmation: [
        '',
        [
          Validators.required,
          //Validators.minLength(5),
          patternValidator(),
        ],
      ],
    },
    {
      validators: MatchPassword('password', 'password_confirmation'),
    }
  );
  //https://arminzia.com/blog/password-validation-with-angular-reactive-forms/
  constructor(
    private fb: FormBuilder, //private customValidator: CustomValidationService
    private authService: AuthService,
    private router: Router,
    private snackbarService: SnackbarService
  ) {}

  ngOnInit(): void {}

  get registerFormControl(): { [key: string]: AbstractControl } {
    return this.registerForm.controls;
  }

  onRegister(): boolean | void {
    this.serverErrors = {};
    this.submitted = true;
    if (this.registerForm.invalid) {
      return true;
    }

    let register: IRegister = this.registerForm.value;
    this.authService.register(register).subscribe({
      next: (result: any) => {
        this.submitted = false;
        this.snackbarService.success(result.message);
        this.router.navigate(['/admin/login']);
      },
      error: (error: any) => {
        this.submitted = false;
        if (error.status === 422 && error.error && error.error.errors) {
          for (let key in error.error.errors) {
            this.registerFormControl[key].setErrors({
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
      },
    });
  }

  onReset() {
    this.submitted = false;
    this.registerForm.reset();
  }
}
