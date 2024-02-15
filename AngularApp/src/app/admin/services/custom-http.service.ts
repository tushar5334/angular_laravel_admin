import { Injectable } from '@angular/core';
import { HttpClient, HttpParams } from '@angular/common/http';
import { Subject, Observable, throwError } from 'rxjs';
import { environment } from 'src/environments/environment';
import { retry, catchError, mergeMap } from 'rxjs/operators';

@Injectable({
  providedIn: 'root',
})
export class CustomHttpService {
  constructor(private http: HttpClient) {}

  get(url: string, queryParams: object = {}): Observable<any> {
    let params = new HttpParams();

    for (const [key, value] of Object.entries(queryParams)) {
      params = params.append(key, value);
    }

    return this.http.get(`${environment.apiUrl}/${url}`, { params: params });
  }

  post(
    url: string,
    requestData: any = {},
    isImage: boolean = false
  ): Observable<any> {
    return this.http.get(`${environment.apiUrl}/csrf-token`).pipe(
      mergeMap((tokenRes: any) => {
        return this.verfiedPostRequest(
          `${environment.apiUrl}/${url}`,
          requestData,
          tokenRes.token,
          isImage
        );
      })
    );
  }

  verfiedPostRequest(
    url: string,
    requestData: any = {},
    token: string,
    isImage: boolean
  ): Observable<any> {
    if (token !== undefined) {
      requestData._token = token;
    }

    let formData: FormData = new FormData();
    if (isImage) {
      for (let key in requestData) {
        let value = requestData[key];
        // multiple file upload
        if (key == 'filesList' && Array.isArray(value)) {
          value.forEach((element, i) => {
            formData.append('filesArray[]', element);
          });
        } else {
          formData.append(key, value);
        }
      }
    } else {
      formData = requestData;
    }
    return this.http.post(url, formData);
  }

  handleError(error: any) {
    let errorMessage: object = {};
    if (error.error instanceof ErrorEvent) {
      // Get client-side error
      errorMessage = {
        status_code: 0,
        message: error.error.message,
      };
    } else {
      errorMessage = {
        status_code: error.status,
        message: error.message,
      };
      // Get server-side error
      //errorMessage = `Error Code: ${error.status}\nMessage: ${error.message}`;
    }
    //window.alert(errorMessage);
    return throwError(() => {
      return errorMessage;
    });
  }
}
