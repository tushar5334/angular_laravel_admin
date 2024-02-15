import { Component, OnInit } from '@angular/core';
import { Validators, FormBuilder, AbstractControl } from '@angular/forms';
import { Router, ActivatedRoute } from '@angular/router';
import { IResetPassword } from '../interfaces/reset-password';
import { AuthService } from '../services/auth.service';
import { SnackbarService } from '../services/snackbar.service';
import {
  patternValidator,
  MatchPassword,
} from '../validators/custom.validators';
@Component({
  selector: 'app-reset-password',
  templateUrl: './reset-password.component.html',
  styleUrls: ['./reset-password.component.scss'],
})
export class ResetPasswordComponent implements OnInit {
  submitted: boolean = false;
  serverErrors: { [key: string]: string } = {};
  resetPasswordForm = this.fb.group(
    {
      reset_token: [''],
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
      validator: MatchPassword('password', 'password_confirmation'),
    }
  );

  constructor(
    private fb: FormBuilder, //private customValidator: CustomValidationService
    private authService: AuthService,
    private router: Router,
    private route: ActivatedRoute,
    private snackbarService: SnackbarService
  ) {}

  ngOnInit(): void {
    let reset_token: any = this.route.snapshot.paramMap.get(
      'reset_token'
    ) as string;

    this.resetPasswordForm.patchValue({
      reset_token: reset_token,
    });
  }

  get resetPasswordFormControl(): { [key: string]: AbstractControl } {
    return this.resetPasswordForm.controls;
  }

  onResetPassword(): void {
    this.serverErrors = {};
    this.submitted = true;
    if (this.resetPasswordForm.invalid) {
      return;
    }

    let resetPassword: IResetPassword = this.resetPasswordForm.value;
    this.authService.resetPassword(resetPassword).subscribe({
      next: (result: any) => {
        this.submitted = false;
        this.snackbarService.success(result.message);
        this.router.navigate(['/admin/login']);
      },
      error: (error: any) => {
        this.submitted = false;
        if (error.status === 422 && error.error && error.error.errors) {
          for (let key in error.error.errors) {
            this.resetPasswordFormControl[key].setErrors({
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
    this.resetPasswordForm.reset();
  }
}
