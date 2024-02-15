import { Injectable } from '@angular/core';
import {
  MatSnackBar,
  MatSnackBarConfig,
  MatSnackBarHorizontalPosition,
  MatSnackBarVerticalPosition,
} from '@angular/material/snack-bar';
@Injectable({
  providedIn: 'root',
})
export class SnackbarService {
  private duration: number = 10000;
  private action: string = 'Dismiss';
  private verticalPosition: MatSnackBarVerticalPosition = 'top';
  private horizontalPosition: MatSnackBarHorizontalPosition = 'center';
  constructor(private snackBar: MatSnackBar) {}

  /* openSnackbar(data: {
    message: string;
    action: string;
    duration: number;
  }): void {
    let sb = this.snackBar.open(data.message, data.action, {
      duration: data.duration,
      verticalPosition: 'top', // Allowed values are  'top' | 'bottom'
      horizontalPosition: 'center', // Allowed values are 'start' | 'center' | 'end' | 'left' | 'right'
      //panelClass: ['custom-style'],
    });
    sb.onAction().subscribe(() => {
      sb.dismiss();
    });
  } */

  success(message: string): void {
    let config = new MatSnackBarConfig();
    config.duration = this.duration;
    config.verticalPosition = this.verticalPosition;
    config.horizontalPosition = this.horizontalPosition;
    config.panelClass = [];
    let sb = this.snackBar.open(message, this.action, config);
    sb.onAction().subscribe(() => {
      sb.dismiss();
    });
  }

  error(message: string): void {
    let sb = this.snackBar.open(message, this.action, {
      duration: this.duration,
      verticalPosition: this.verticalPosition, // Allowed values are  'top' | 'bottom'
      horizontalPosition: this.horizontalPosition, // Allowed values are 'start' | 'center' | 'end' | 'left' | 'right'
      panelClass: [],
    });
    sb.onAction().subscribe(() => {
      sb.dismiss();
    });
  }

  info(message: string): void {
    let sb = this.snackBar.open(message, this.action, {
      duration: this.duration,
      verticalPosition: this.verticalPosition, // Allowed values are  'top' | 'bottom'
      horizontalPosition: this.horizontalPosition, // Allowed values are 'start' | 'center' | 'end' | 'left' | 'right'
      panelClass: [],
    });
    sb.onAction().subscribe(() => {
      sb.dismiss();
    });
  }

  openSnackbar() {
    console.log('openSnackbar');
  }
}
