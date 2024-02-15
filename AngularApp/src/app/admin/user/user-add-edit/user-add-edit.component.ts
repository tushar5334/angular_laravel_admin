import { Component, OnInit } from '@angular/core';
import { Router, ActivatedRoute } from '@angular/router';
import { Validators, FormBuilder, AbstractControl } from '@angular/forms';
import { UserService } from '../../services/user.service';
import { AuthService } from '../../services/auth.service';
import { SnackbarService } from '../../services/snackbar.service';
import { LoaderService } from '../../services/loader.service';
import { IUserAddEdit } from '../../interfaces/user-add-edit';
import {
  patternValidator,
  MatchPassword,
} from '../../validators/custom.validators';
import { ConfirmationDialogComponent } from 'src/app/shared/admin/components/confirmation-dialog/confirmation-dialog.component';
import { MatDialog } from '@angular/material/dialog';
@Component({
  selector: 'app-user-add-edit',
  templateUrl: './user-add-edit.component.html',
  styleUrls: ['./user-add-edit.component.scss'],
})
export class UserAddEditComponent implements OnInit {
  submitted: boolean = false;
  public files: any[] = [];
  private user_id: string = '';
  display_picture: string | undefined = '';
  btnText: string = 'Save';
  titleText: string = 'Add';
  serverErrors: { [key: string]: string } = {};
  userForm = this.fb.group(
    {
      user_id: ['0'],
      name: ['', [Validators.required]],
      email: ['', [Validators.required, Validators.email]],
      user_type: ['', [Validators.required]],
      password: [''],
      password_confirmation: [''],
      status: [false],
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
    private userService: UserService,
    private authService: AuthService,
    private snackbarService: SnackbarService,
    private loaderService: LoaderService,
    public dialog: MatDialog
  ) {}

  ngOnInit(): void {
    this.user_id = this.route.snapshot.paramMap.get('user_id') || '0';
    // Change lables for button and title
    this.btnText = this.user_id != '0' ? 'Update' : 'Save';
    this.titleText = this.user_id != '0' ? 'Edit' : 'Add';

    // Set validations for password and confirm password field
    this.updateValidations();

    this.userService.getInitData(this.user_id).subscribe({
      next: (result: any) => {
        if (result.data) {
          let data: IUserAddEdit = result.data;
          this.userForm.patchValue({
            user_id: data.user_id,
            name: data.name,
            email: data.email,
            user_type: data.user_type,
            status: data.status,
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
    return index; // unique id corresponding to the item
  }

  get userFormControl(): { [key: string]: AbstractControl } {
    return this.userForm.controls;
  }

  updateValidations() {
    if (this.user_id == '0') {
      // Set required and pattern validation for password and confirm password field
      this.userFormControl['password'].setValidators([
        Validators.required,
        patternValidator(),
      ]);
      this.userFormControl['password'].updateValueAndValidity();

      this.userFormControl['password_confirmation'].setValidators([
        Validators.required,
        patternValidator(),
      ]);
      this.userFormControl['password_confirmation'].updateValueAndValidity();
    } else {
      // Set pattern validation for password and confirm password field
      this.userFormControl['password'].setValidators([patternValidator()]);
      this.userFormControl['password'].updateValueAndValidity();

      this.userFormControl['password_confirmation'].setValidators([
        patternValidator(),
      ]);
      this.userFormControl['password_confirmation'].updateValueAndValidity();
    }
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

  onSave(): boolean | void {
    this.submitted = true;
    this.serverErrors = {};
    if (this.userForm.invalid) {
      return true;
    }

    this.loaderService.showLoader();
    let userData = this.userForm.value as IUserAddEdit;
    userData.filesList = this.files;

    this.userService.createOrUpdareRecord(userData).subscribe({
      next: (result: any) => {
        this.submitted = false;
        this.loaderService.hideLoader();
        this.snackbarService.success(result.message);
        this.router.navigate(['/admin/users']);
      },
      error: (error: any) => {
        this.submitted = false;
        this.loaderService.hideLoader();
        if (error.status === 422 && error.error && error.error.errors) {
          for (let key in error.error.errors) {
            this.userFormControl[key].setErrors({
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
    this.userForm.reset();
    this.updateValidations();
  }
}
