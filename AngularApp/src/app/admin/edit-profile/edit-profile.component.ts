import { Component, OnInit } from '@angular/core';
import { Router, ActivatedRoute } from '@angular/router';
import { Validators, FormBuilder, AbstractControl } from '@angular/forms';
import { AuthService } from '../services/auth.service';
import { UserService } from '../services/user.service';
import { SnackbarService } from '../services/snackbar.service';
import { LoaderService } from '../services/loader.service';
import {
  patternValidator,
  MatchPassword,
} from '../validators/custom.validators';
import { ConfirmationDialogComponent } from 'src/app/shared/admin/components/confirmation-dialog/confirmation-dialog.component';
import { MatDialog } from '@angular/material/dialog';
import { IEditProfile } from '../interfaces/edit-profile';
@Component({
  selector: 'app-edit-profile',
  templateUrl: './edit-profile.component.html',
  styleUrls: ['./edit-profile.component.scss'],
})
export class EditProfileComponent implements OnInit {
  submitted: boolean = false;
  public files: any[] = [];
  display_picture: string | undefined = '';
  serverErrors: { [key: string]: string } = {};
  editProfileForm = this.fb.group(
    {
      name: ['', [Validators.required]],
      email: ['', [Validators.required, Validators.email]],
      password: [''],
      password_confirmation: [''],
      profile_picture: [''],
      filesList: this.fb.array([]),
    },
    {
      validator: MatchPassword('password', 'password_confirmation'),
    }
  );
  constructor(
    private router: Router,
    private route: ActivatedRoute,
    private fb: FormBuilder,
    private authService: AuthService,
    private userService: UserService,
    private snackbarService: SnackbarService,
    private loaderService: LoaderService,
    public dialog: MatDialog
  ) {}

  ngOnInit(): void {
    // Set validations for password and confirm password field
    this.updateValidations();

    this.userService.getCurrentUserDetail().subscribe({
      next: (result: any) => {
        if (result.data) {
          let data: IEditProfile = result.data;
          this.editProfileForm.patchValue({
            name: data.name,
            email: data.email,
            profile_picture:
              data.profile_picture != '' && data.profile_picture != null
                ? data.profile_picture
                : '',
          });

          this.display_picture = data.display_picture;
        }
      },
      error: (error: any) => {
        if (error.status === 401) {
          let message: string =
            error.message && error.error.message
              ? error.error.message
              : error.message;
          this.authService.clientSideLogout();
          this.snackbarService.error(message);
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

  trackByFn(index: number, item: any) {
    return index; // index corresponding to the item
    //return item.id; // unique id corresponding to the item
  }

  get editProfileFormControl(): { [key: string]: AbstractControl } {
    return this.editProfileForm.controls;
  }

  updateValidations() {
    // Set pattern validation for password and confirm password field
    this.editProfileFormControl['password'].setValidators([patternValidator()]);
    this.editProfileFormControl['password'].updateValueAndValidity();

    this.editProfileFormControl['password_confirmation'].setValidators([
      patternValidator(),
    ]);
    this.editProfileFormControl[
      'password_confirmation'
    ].updateValueAndValidity();
  }

  onFileChange(e: any) {
    let pFileList: any[];
    if (e.target) {
      pFileList = e.target.files;
    } else {
      pFileList = e;
    }
    this.files = Object.keys(pFileList).map((key: any) => pFileList[key]);
  }

  deleteFile(f: any) {
    this.files = this.files.filter(function (w) {
      return w.name != f.name;
    });
  }

  openConfirmDialog(pIndex: number): void {
    const dialogRef = this.dialog.open(ConfirmationDialogComponent, {
      width: '300px',
      disableClose: true,
      data: {
        message: `${this.files[pIndex].name} file will be deleted?`,
        buttonText: {
          ok: 'Confirm',
          cancel: 'Cancel',
        },
      },
    });

    dialogRef.afterClosed().subscribe((confirmed: any) => {
      if (confirmed) {
        this.deleteFromArray(pIndex);
      }
    });
  }

  deleteFromArray(index: any) {
    this.files.splice(index, 1);
  }

  onRegister(): boolean | void {
    this.submitted = true;
    this.serverErrors = {};
    if (this.editProfileForm.invalid) {
      return true;
    }

    this.loaderService.showLoader();
    let userData = this.editProfileForm.value as IEditProfile;
    userData.filesList = this.files;

    this.userService.updateProfile(userData).subscribe({
      next: (result: any) => {
        this.submitted = false;
        this.loaderService.hideLoader();
        this.authService.setItemToLocalStorage(
          'user',
          JSON.stringify(result.user)
        );
        this.authService.updateUserData(result.user);
        this.snackbarService.success(result.message);
        this.router.navigate(['/admin']);
      },
      error: (error: any) => {
        this.submitted = false;
        this.loaderService.hideLoader();
        if (error.status === 422 && error.error && error.error.errors) {
          for (let key in error.error.errors) {
            this.editProfileFormControl[key].setErrors({
              serverValidationError: true,
            });
            this.serverErrors[key] = error.error.errors[key][0] as string;
          }
        } else if (error.status === 401) {
          let message: string =
            error.message && error.error.message
              ? error.error.message
              : error.message;
          this.authService.clientSideLogout();
          this.snackbarService.error(message);
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
    this.editProfileForm.reset();
    this.updateValidations();
  }
}
