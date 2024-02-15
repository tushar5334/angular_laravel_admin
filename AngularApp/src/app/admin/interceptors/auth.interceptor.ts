import { Injectable } from '@angular/core';
import {
  HttpRequest,
  HttpHandler,
  HttpEvent,
  HttpInterceptor,
  HttpErrorResponse,
} from '@angular/common/http';
import { Observable, throwError, of, retry } from 'rxjs';
import { catchError, map, tap } from 'rxjs/operators';
import { AuthService } from '../services/auth.service';
import { Router } from '@angular/router';
@Injectable()
export class AuthInterceptor implements HttpInterceptor {
  constructor(private authService: AuthService, private router: Router) {}

  intercept(
    request: HttpRequest<any>,
    next: HttpHandler
  ): Observable<HttpEvent<any>> {
    const authToken = this.authService.getToken();
    const authRequest = request.clone({
      headers: request.headers.set('Authorization', 'Bearer ' + authToken),
    });
    return next.handle(authRequest);
    /* return next.handle(authRequest).pipe(
      // tap((data: any) => {
      //   console.log('data', data);
      // }),
      catchError((err) => {
        if (err.status === 401) {
          this.handleError(err);
        }

        return throwError(() => new Error(err));
      })
    ); */
  }
  /* handleError(error: any) {
    this.authService.clientSideLogout();
    this.router.navigate(['admin/login']);
  } */
}
