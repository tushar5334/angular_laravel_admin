import {
  Component,
  OnInit,
  Output,
  EventEmitter,
  OnDestroy,
} from '@angular/core';
import { AuthService } from 'src/app/admin/services/auth.service';
import { SnackbarService } from 'src/app/admin/services/snackbar.service';
import { Subscription } from 'rxjs';
import { ITheme } from 'src/app/admin/interfaces/theme';
import { SettingsService } from 'src/app/admin/services/settings.service';
import { ThemePalette } from '@angular/material/core';
@Component({
  selector: 'app-header',
  templateUrl: './header.component.html',
  styleUrls: ['./header.component.scss'],
})
export class HeaderComponent implements OnInit, OnDestroy {
  color: ThemePalette = 'warn';
  activeTheme!: string;
  themeList: ITheme[] = [
    {
      themeclass: 'theme-deeppurple-amber-light',
      themeLabel: 'Deep Purple & Amber',
      isThemeActive: true,
    },
    {
      themeclass: 'theme-indigo-pink-light',
      themeLabel: 'Indigo & Pink',
      isThemeActive: false,
    },
    {
      themeclass: 'theme-pink-bluegray-dark',
      themeLabel: 'Pink & Blue',
      isThemeActive: false,
    },
    {
      themeclass: 'theme-purple-green-dark',
      themeLabel: 'Purple & Green',
      isThemeActive: false,
    },
    {
      themeclass: 'theme-custom-light',
      themeLabel: 'Custom Light',
      isThemeActive: false,
    },
    {
      themeclass: 'theme-custom-dark',
      themeLabel: 'Custom Dark',
      isThemeActive: false,
    },
  ];
  userIsAuthenticated: boolean = false;
  private authListnerSub!: Subscription;

  @Output() toggleSideBarEmitter: EventEmitter<any> = new EventEmitter();
  constructor(
    private authService: AuthService,
    private snackbarService: SnackbarService,
    private settingsService: SettingsService
  ) {}

  ngOnInit(): void {
    this.activeTheme =
      this.authService.getItemFromLocalStorage('current_theme') ||
      'theme-deeppurple-amber-light';

    this.themeList.forEach((obj) => {
      obj.isThemeActive = false;
      if (obj.themeclass == this.activeTheme) {
        obj.isThemeActive = true;
      }
    });

    this.userIsAuthenticated = this.authService.getIsAuth();
    this.authListnerSub = this.authService.getAuthStatusListner().subscribe({
      next: (isAuthenticated: boolean) => {
        this.userIsAuthenticated = isAuthenticated;
      },
      error: (error: any) => {
        this.snackbarService.error(
          'Something went wrong, Please try again later.'
        );
        this.onLogout();
      },
    });
  }

  toggleSideBar() {
    this.toggleSideBarEmitter.emit();
    setTimeout(() => {
      window.dispatchEvent(new Event('resize'));
    }, 300);
  }

  onLogout(): void {
    //this.snackbarService.success('Sucessfully logged in!!!😃');
    this.authService.logout().subscribe({
      next: (result: any) => {
        // Clears local storage data and redirect to login page
        this.authService.clientSideLogout();
        this.snackbarService.success(result.message);
      },
      error: (error: any) => {
        if (error.status === 422 && error.message) {
          this.snackbarService.error(error.error.message);
        } else {
          this.snackbarService.error(
            'Something went wrong, Please try again later.'
          );
        }
      },
    });
  }

  onThemeChange(themeclass: string) {
    this.settingsService.changeTheme(themeclass);
  }

  trackByFn(index: number, item: any) {
    return index; // unique id corresponding to the item
  }

  ngOnDestroy() {
    this.authListnerSub.unsubscribe();
  }
}
