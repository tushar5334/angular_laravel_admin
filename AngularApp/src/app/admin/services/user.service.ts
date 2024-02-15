import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Subject, Observable, throwError } from 'rxjs';
import { retry, catchError } from 'rxjs/operators';
import { IUserFilter } from '../interfaces/user-filter';
import { CustomHttpService } from './custom-http.service';
import { SortDirection } from '@angular/material/sort';
@Injectable({
  providedIn: 'root',
})
export class UserService {
  private moduleUrl: string = 'users';
  constructor(
    private customHttpService: CustomHttpService,
    private http: HttpClient
  ) {}

  getUsers(
    currentPage: number,
    pageSize: number,
    sortField: string,
    sortDirection: SortDirection,
    filterData: IUserFilter
  ): Observable<any> {
    let queryParams: object = {
      currentPage,
      pageSize,
      sortField,
      sortDirection,
      globalFilter: filterData.globalFilter,
      startDate: filterData.startDate,
      endDate: filterData.endDate,
    };

    return this.customHttpService.get(`${this.moduleUrl}`, queryParams);
  }

  exportUsers(
    sortField: string,
    sortDirection: SortDirection,
    filterData: IUserFilter
  ): Observable<any> {
    let queryParams: object = {
      sortField,
      sortDirection,
      globalFilter: filterData.globalFilter,
      startDate: filterData.startDate,
      endDate: filterData.endDate,
    };

    return this.customHttpService.get(
      `${this.moduleUrl}/export-user`,
      queryParams
    );
  }

  getInitData(user_id: string): Observable<any> {
    return this.customHttpService.get(`${this.moduleUrl}/${user_id}/edit`);
  }
  deleteRecord(postData: any): Observable<any> {
    return this.customHttpService.post(`${this.moduleUrl}/delete`, postData);
  }

  changeStatus(user_id: string): Observable<any> {
    return this.customHttpService.get(
      `${this.moduleUrl}/change-status/${user_id}`
    );
  }

  createOrUpdareRecord(postData: any): Observable<any> {
    return this.customHttpService.post(
      `${this.moduleUrl}/create-update`,
      postData,
      true
    );
  }

  updateAllRecords(postData: any): Observable<any> {
    return this.customHttpService.post(
      `${this.moduleUrl}/multiple-change-status`,
      postData
    );
  }

  deleteAllRecords(postData: any): Observable<any> {
    return this.customHttpService.post(
      `${this.moduleUrl}/mutiple-delete`,
      postData
    );
  }

  getCurrentUserDetail(): Observable<any> {
    return this.customHttpService.get(`${this.moduleUrl}/me`);
  }

  updateProfile(postData: any): Observable<any> {
    return this.customHttpService.post(
      `${this.moduleUrl}/update-profile`,
      postData,
      true
    );
  }
}
