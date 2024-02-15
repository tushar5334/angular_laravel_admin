import { Component, OnInit } from '@angular/core';
import { Validators, FormBuilder, AbstractControl } from '@angular/forms';
import { Router } from '@angular/router';
import { AuthService } from '../services/auth.service';
import { SnackbarService } from '../services/snackbar.service';
import { IForgotPassword } from '../interfaces/forgot-password';

@Component({
  selector: 'app-forgot-password',
  templateUrl: './forgot-password.component.html',
  styleUrls: ['./forgot-password.component.scss'],
})
export class ForgotPasswordComponent implements OnInit {
  submitted: boolean = false;
  serverErrors: { [key: string]: string } = {};
  forgotPasswordForm = this.fb.group({
    email: ['', [Validators.required, Validators.email]],
  });

  constructor(
    private fb: FormBuilder,
    private authService: AuthService,
    private router: Router,
    private snackbarService: SnackbarService
  ) {}

  ngOnInit(): void {}
  get forgotPasswordFormControl(): { [key: string]: AbstractControl } {
    return this.forgotPasswordForm.controls;
  }

  onForgotPassword(): boolean | void {
    this.serverErrors = {};
    this.submitted = true;
    if (this.forgotPasswordForm.invalid) {
      return true;
    }

    let forgotPassword = this.forgotPasswordForm.value as IForgotPassword;

    this.authService.forgotPassword(forgotPassword).subscribe({
      next: (result: any) => {
        this.submitted = false;
        this.snackbarService.success(result.message);
        this.router.navigate(['/admin/login']);
      },
      error: (error: any) => {
        this.submitted = false;
        if (error.status === 422 && error.error && error.error.errors) {
          for (let key in error.error.errors) {
            this.forgotPasswordFormControl[key].setErrors({
              serverValidationError: true,
            });
            this.serverErrors[key] = error.error.errors[key][0] as string;
          }
        } else if (error.status === 500) {
          let message: string =
            error.message && error.error.message
              ? error.error.message
              : error.message;
          this.snackbarService.error(message);
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
    this.forgotPasswordForm.reset();
  }
}
