import { Component, OnInit, OnDestroy } from '@angular/core';
import { Subscription } from 'rxjs';
import { SettingsService } from './admin/services/settings.service';
import { AuthService } from './admin/services/auth.service';
import {
  SwPush,
  SwUpdate,
  UnrecoverableStateEvent,
  VersionEvent,
  VersionReadyEvent,
} from '@angular/service-worker';
@Component({
  selector: 'app-root',
  templateUrl: './app.component.html',
  styleUrls: ['./app.component.scss'],
})
export class AppComponent implements OnInit, OnDestroy {
  activeTheme: string;
  private themeListnerSub!: Subscription;
  constructor(
    private settingsService: SettingsService,
    private authService: AuthService,
    private updateService: SwUpdate,
    private pushService: SwPush
  ) {
    this.activeTheme =
      this.authService.getItemFromLocalStorage('current_theme') ||
      'theme-deeppurple-amber-light';
  }

  ngOnInit(): void {
    // Service Worker code start //
    console.log('AppComponent.ngOnInit');
    if (!this.updateService.isEnabled) {
      console.log('AppComponent.ngOnInit: Service Worker is not enabled');
      return;
    }
    console.log('AppComponent.ngOnInit: Service Worker is enabled');
    this.handleUpdates();
    // Service Worker code end //

    this.themeListnerSub = this.settingsService.themeChangeListner.subscribe({
      next: (themeClassName: string) => {
        this.activeTheme = themeClassName;
      },
      error: (error: any) => {
        console.log('error themeListnerSub');
      },
    });
  }

  /* handleUpdates() {
    this.updateService.versionUpdates.subscribe((event: VersionEvent) => {
      console.log(event);
      alert(event.type);
      if (
        event.type === 'VERSION_READY' &&
        confirm(
          `New version ${
            (event as VersionReadyEvent).latestVersion.hash
          } available. Load New Version?`
        )
      ) {
        window.location.reload();
      }
    });
  }
 */

  handleUpdates() {
    this.updateService.versionUpdates.subscribe((event: VersionEvent) => {
      console.log(event);
      if (event.type === 'VERSION_READY') {
        window.location.reload();
      }
    });
  }

  ngOnDestroy() {
    this.themeListnerSub.unsubscribe();
  }
}
