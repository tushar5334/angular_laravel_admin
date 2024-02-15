import { Component, OnInit } from '@angular/core';
import { AuthService } from './services/auth.service';
import { BreakpointObserver, BreakpointState } from '@angular/cdk/layout';
@Component({
  selector: 'app-layout',
  templateUrl: './layout.component.html',
  styleUrls: ['./layout.component.scss'],
})
export class LayoutComponent implements OnInit {
  sideBarOpen: boolean = true;
  constructor(
    private authService: AuthService,
    public breakpointObserver: BreakpointObserver
  ) {}

  ngOnInit(): void {
    this.authService.autoAuthUser();
    this.breakpointObserver
      .observe(['(min-width: 821px)'])
      .subscribe((state: BreakpointState) => {
        if (state.matches) {
          this.sideBarOpen = true;
          //console.log('Viewport width is 500px or greater!');
        } else {
          //console.log('Viewport width is less than 500px!');
          this.sideBarOpen = false;
        }
      });
  }

  sideBarToggler(event: Event) {
    this.sideBarOpen = !this.sideBarOpen;
  }
}
