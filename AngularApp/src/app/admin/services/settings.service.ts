import { Injectable } from '@angular/core';
import { Subject } from 'rxjs';
import { AuthService } from './auth.service';
@Injectable({
  providedIn: 'root',
})
export class SettingsService {
  public themeChangeListner = new Subject<string>();

  constructor(private authService: AuthService) {}

  changeTheme(className: string) {
    this.themeChangeListner.next(className);
    this.authService.setItemToLocalStorage('current_theme', className);
  }
}
