import { Injectable } from '@angular/core';
import { HttpClient, HttpHeaders } from '@angular/common/http';
import { Subject, Observable, throwError } from 'rxjs';
import { Router } from '@angular/router';
import { environment } from 'src/environments/environment';
import { retry, catchError } from 'rxjs/operators';
import { CustomHttpService } from './custom-http.service';
import { ILogin } from '../interfaces/login';
import { IRegister } from '../interfaces/register';
import { IForgotPassword } from '../interfaces/forgot-password';
import { IResetPassword } from '../interfaces/reset-password';
import { IUser } from '../interfaces/user';
@Injectable({
  providedIn: 'root',
})
export class AuthService {
  private isAuthenticated = false;
  private token: string | null = '';
  private userData: object | null = {};
  private authStatusListner = new Subject<boolean>();
  public userDataListner = new Subject<any>();

  constructor(
    private customHttpService: CustomHttpService,
    private router: Router,
    private http: HttpClient
  ) {}

  getToken() {
    return this.token;
  }

  getAuthStatusListner() {
    return this.authStatusListner.asObservable();
  }

  getIsAuth() {
    return this.isAuthenticated;
  }

  getUserData() {
    return this.userData;
  }
  setAuthData(token: string = '', user: any, authListnerStatus: boolean): void {
    if (token) {
      this.token = token;
      this.isAuthenticated = true;
      this.userData = user;
      this.authStatus(authListnerStatus);
      this.saveAuthData(token, user);
    }
  }

  authStatus(authListnerStatus: boolean = false) {
    this.authStatusListner.next(authListnerStatus);
  }

  updateUserData(data: any) {
    this.userDataListner.next(data);
  }

  private saveAuthData(token: string, user: any) {
    this.setItemToLocalStorage('token', token);
    this.setItemToLocalStorage('user', JSON.stringify(user));
  }

  private clearAuthData() {
    this.removeItemFromLocalStorage('token');
    this.removeItemFromLocalStorage('user');
  }

  getAuthData() {
    const token: string | null = localStorage.getItem('token');
    const user: any = localStorage.getItem('user');
    if (!token) {
      return;
    }
    return {
      token,
      user: JSON.parse(user),
    };
  }

  setItemToLocalStorage(key: string, data: any) {
    localStorage.setItem(key, data);
  }

  getItemFromLocalStorage(key: string) {
    return localStorage.getItem(key);
  }

  removeItemFromLocalStorage(key: string) {
    localStorage.removeItem(key);
  }

  autoAuthUser() {
    const authInformation = this.getAuthData();
    if (!authInformation) {
      this.clientSideLogout();
      return;
    }

    this.token = authInformation.token;
    this.isAuthenticated = true;
    this.userData = authInformation.user;
    this.authStatus(true);
  }

  clientSideLogout() {
    this.token = null;
    this.isAuthenticated = false;
    this.userData = null;
    this.clearAuthData();
    this.authStatus(false);
    this.router.navigate(['/admin/login']);
  }
  //Demo get call
  /* csrfToken(): Observable<{ message: string }> {
    return this.http.get<{ message: string }>(
      `${environment.apiUrl}/csrf-token`,
      {}
    );
    //return this.http.get(`${environment.apiUrl}/login`, authData, {}).pipe(retry(1), catchError(this.handleError));
  } */

  //Demo post call
  /* csrfToken(): Observable<{ message: string, error?:object }> {
    return this.http.post<{ message: string, error?:object }>(
      `${environment.apiUrl}/csrf-token`,
      {}
    );
    //return this.http.post(`${environment.apiUrl}/login`, authData, {}).pipe(retry(1), catchError(this.handleError));
  } */

  login(postData: ILogin): Observable<any> {
    return this.customHttpService.post(`login`, postData);
  }

  logout(): Observable<any> {
    return this.customHttpService.post(`logout`);
  }

  register(postData: IRegister): Observable<any> {
    return this.customHttpService.post(`register`, postData);
  }

  forgotPassword(postData: IForgotPassword): Observable<any> {
    return this.customHttpService.post(`forgot-password`, postData);
  }

  resetPassword(postData: IResetPassword): Observable<any> {
    return this.customHttpService.post(`reset-password`, postData);
  }

  // Error handling
  handleError(error: any) {
    let errorMessage = '';
    if (error.error instanceof ErrorEvent) {
      // Get client-side error
      errorMessage = error.error.message;
    } else {
      // Get server-side error
      errorMessage = `Error Code: ${error.status}\nMessage: ${error.message}`;
    }
    window.alert(errorMessage);
    return throwError(() => {
      return errorMessage;
    });
  }
}
