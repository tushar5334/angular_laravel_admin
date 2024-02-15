import {
  Component,
  OnInit,
  AfterViewInit,
  ViewChild,
  ElementRef,
} from '@angular/core';
import { FormBuilder, AbstractControl } from '@angular/forms';
import { formatDate } from '@angular/common';
import { IUser } from '../../interfaces/user';
import { IUserFilter } from '../../interfaces/user-filter';
import { UserService } from '../../services/user.service';
import { SnackbarService } from '../../services/snackbar.service';
import { LoaderService } from '../../services/loader.service';
import { MatTableDataSource } from '@angular/material/table';
import { MatPaginator, PageEvent } from '@angular/material/paginator';
import { MatSort, Sort, SortDirection } from '@angular/material/sort';
import { MatSlideToggleChange } from '@angular/material/slide-toggle';
import { ConfirmationDialogComponent } from 'src/app/shared/admin/components/confirmation-dialog/confirmation-dialog.component';
import { MatDialog } from '@angular/material/dialog';
import { AuthService } from '../../services/auth.service';
import { environment } from 'src/environments/environment';
@Component({
  selector: 'app-user-list',
  templateUrl: './user-list.component.html',
  styleUrls: ['./user-list.component.scss'],
})
export class UserListComponent implements OnInit, AfterViewInit {
  filterForm = this.fb.group({
    globalFilter: [''],
    startDate: [''],
    endDate: [''],
  });
  originalUserData: IUser[] = [];
  allSelected: boolean = false;
  totalRows: number = 0;
  pageSize: number = 10;
  currentPage: number = 0;
  sortField: string = 'created_at';
  sortDirection: SortDirection = 'desc';
  pageSizeOptions: number[] = [5, 10, 25, 100];
  displayedColumns: string[] = [
    'selectAll',
    'index',
    'name',
    'display_picture',
    'email',
    'user_type',
    'created_at',
    'status',
    'action',
  ];
  dataSource: MatTableDataSource<IUser> = new MatTableDataSource<IUser>([]);
  @ViewChild(MatPaginator) paginator!: MatPaginator;
  @ViewChild(MatSort) sort!: MatSort;

  constructor(
    private fb: FormBuilder,
    private authService: AuthService,
    private userService: UserService,
    private snackbarService: SnackbarService,
    private loaderService: LoaderService,
    public dialog: MatDialog
  ) {}

  ngOnInit(): void {}

  getUserData() {
    this.loaderService.showLoader();
    this.userService
      .getUsers(
        this.currentPage,
        this.pageSize,
        this.sortField,
        this.sortDirection,
        this.filterForm.value
      )
      .subscribe({
        next: (result: any) => {
          this.dataSource = new MatTableDataSource(result.data);
          this.paginator.pageIndex = this.currentPage;
          this.paginator.length = result.totalCount;
          this.originalUserData = this.dataSource.data;
          this.loaderService.hideLoader();
          //this.snackbarService.success(result.message);
        },
        error: (error: any) => {
          this.dataSource = new MatTableDataSource<IUser>([]);
          this.loaderService.hideLoader();
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

  deleteRecord(user_id: string) {
    this.loaderService.showLoader();
    this.userService.deleteRecord({ user_id }).subscribe({
      next: (result: { message: string }) => {
        this.loaderService.hideLoader();
        this.snackbarService.success(result.message);
        this.getUserData();
      },
      error: (error: any) => {
        this.loaderService.hideLoader();
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

  ediSingleRecord(user_id: string) {
    console.log('user_id', user_id);
  }
  changeStatus(event: MatSlideToggleChange, user_id: string) {
    this.loaderService.showLoader();
    this.userService.changeStatus(user_id).subscribe({
      next: (result: { message: string }) => {
        this.loaderService.hideLoader();
        this.snackbarService.success(result.message);
      },
      error: (error: any) => {
        event.source.checked = !event.checked;
        this.loaderService.hideLoader();
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
    /* console.log('status', event.checked);
    console.log('user_id', user_id); */
  }
  pageChanged(event: PageEvent) {
    console.log({ event });
    this.pageSize = event.pageSize;
    this.currentPage = event.pageIndex;
    this.getUserData();
  }

  get filterFormControl(): { [key: string]: AbstractControl } {
    return this.filterForm.controls;
  }

  onSearch(): void {
    this.filterForm.value as IUserFilter;
    this.filterForm.value.startDate = this.filterForm.value.startDate
      ? formatDate(this.filterForm.value.startDate, 'yyyy-MM-dd', 'en-US')
      : '';

    this.filterForm.value.endDate = this.filterForm.value.endDate
      ? formatDate(this.filterForm.value.endDate, 'yyyy-MM-dd', 'en-US')
      : '';
    this.getUserData();
  }

  onReset() {
    this.filterForm.reset();
    this.getUserData();
  }
  onExportAll() {
    let filterData: IUserFilter = this.filterForm.value;
    window.open(
      encodeURI(
        `${environment.apiUrl}/users/export-user?sortField=${this.sortField}&sortDirection=${this.sortDirection}&globalFilter=${filterData.globalFilter}&startDate=${filterData.startDate}&endDate=${filterData.endDate}`
      ),
      '_blank'
    );
  }
  checkUncheckAll(isChecked: boolean) {
    this.allSelected = isChecked;
    if (this.dataSource.data.length > 0) {
      this.dataSource.data.forEach(
        (element) => (element.is_selected = isChecked)
      );
    }
  }

  updateAllSelected() {
    this.allSelected = this.dataSource.data.every(
      (element) => element.is_selected
    );
  }

  someChecked() {
    /* if (this.dataSource.data == null) {
      return false;
    } */
    return (
      this.dataSource.data.filter((value) => value.is_selected).length > 0 &&
      !this.allSelected
    );
  }

  onDeleteAll() {
    if (this.dataSource.data.filter((value) => value.is_selected).length > 0) {
      let selectedData: IUser[] = this.dataSource.data.filter(
        (value) => value.is_selected === true
      );

      const dialogRef = this.dialog.open(ConfirmationDialogComponent, {
        width: '300px',
        disableClose: true,
        data: {
          message: 'Are you sure want to delete this records?',
          buttonText: {
            ok: 'Yes',
            cancel: 'No',
          },
        },
      });

      dialogRef.afterClosed().subscribe((confirmed: any) => {
        if (confirmed) {
          this.deleteAllRecords(selectedData);
        } else {
          //Reset Selection
          this.checkUncheckAll(false);
        }
      });
    } else {
      this.snackbarService.error('Please choose at least one record to delete');
    }
  }

  deleteAllRecords(selectedData: IUser[] = []) {
    let Ids: any[] = [];
    this.loaderService.showLoader();
    Ids = selectedData.map((element) => element.user_id);
    this.userService.deleteAllRecords({ Ids }).subscribe({
      next: (result: { message: string }) => {
        this.paginator.pageIndex = 0;
        this.currentPage = 0;
        this.allSelected = false;
        this.loaderService.hideLoader();
        this.getUserData();
        this.snackbarService.success(result.message);
      },
      error: (error: any) => {
        //Reset Selection
        this.checkUncheckAll(false);
        this.loaderService.hideLoader();
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
    /* console.log('status', event.checked);
    console.log('user_id', user_id); */
  }

  onUpdateStatusAll(status: number) {
    if (this.dataSource.data.filter((value) => value.is_selected).length > 0) {
      let selectedData: IUser[] = this.dataSource.data.filter(
        (value) => value.is_selected === true
      );

      const dialogRef = this.dialog.open(ConfirmationDialogComponent, {
        width: '300px',
        disableClose: true,
        data: {
          message: 'Are you sure want to change status of records?',
          buttonText: {
            ok: 'Yes',
            cancel: 'No',
          },
        },
      });

      dialogRef.afterClosed().subscribe((confirmed: any) => {
        if (confirmed) {
          this.updateAllRecords(selectedData, status);
        } else {
          //Reset Selection
          this.checkUncheckAll(false);
        }
      });
    } else {
      this.snackbarService.error(
        'Please choose at least one record to update status'
      );
    }
  }

  updateAllRecords(selectedData: IUser[] = [], status: number) {
    let Ids: any[] = [];
    this.loaderService.showLoader();
    Ids = selectedData.map((element) => element.user_id);
    this.userService.updateAllRecords({ Ids, status }).subscribe({
      next: (result: { message: string }) => {
        //this.paginator.pageIndex = 0;
        //this.currentPage = 0;
        this.allSelected = false;
        this.loaderService.hideLoader();
        this.getUserData();
        this.snackbarService.success(result.message);
      },
      error: (error: any) => {
        //Reset Selection
        this.checkUncheckAll(false);
        //this.resetSatatusOnerror(this.originalUserData);
        this.loaderService.hideLoader();
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
    /* console.log('status', event.checked);
    console.log('user_id', user_id); */
  }

  cl(datas: any) {
    alert(datas.user_id);
  }

  openConfirmDialog(user_id: string): void {
    const dialogRef = this.dialog.open(ConfirmationDialogComponent, {
      width: '300px',
      disableClose: true,
      data: {
        message: 'Are you sure want to delete this record?',
        buttonText: {
          ok: 'Yes',
          cancel: 'No',
        },
      },
    });

    dialogRef.afterClosed().subscribe((confirmed: any) => {
      if (confirmed) {
        //console.log(confirmed);
        this.deleteRecord(user_id);
      }
    });
  }

  resetSatatusOnerror(originalUserData: IUser[] = []) {
    this.dataSource.data = originalUserData;
    this.dataSource.data.forEach(
      (element) => (element.status = element.status)
    );
  }

  ngAfterViewInit() {
    this.dataSource.paginator = this.paginator;
    this.dataSource.sort = this.sort;

    this.sort.sortChange.subscribe((sort: Sort) => {
      this.paginator.pageIndex = 0;
      this.currentPage = 0;
      this.sortField = this.sort.active;
      this.sortDirection = this.sort.direction ? this.sort.direction : 'asc';
      this.getUserData();
    });

    this.paginator.page.subscribe((event: PageEvent) => {
      //console.log('paginator ', event);
      this.pageSize = event.pageSize;
      this.currentPage = event.pageIndex;
      this.getUserData();
    });

    this.getUserData();
  }
}
