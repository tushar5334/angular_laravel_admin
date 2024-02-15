import { NgModule } from '@angular/core';
import { RouterModule, Routes } from '@angular/router';
import { ForgotPasswordComponent } from './forgot-password/forgot-password.component';
import { LayoutComponent } from './layout.component';
import { LoginComponent } from './login/login.component';
import { RegisterComponent } from './register/register.component';
import { ResetPasswordComponent } from './reset-password/reset-password.component';
import { AuthGuard } from './guards/auth.guard';
import { GuestGuard } from './guards/guest.guard';
const routes: Routes = [
  {
    path: 'login',
    component: LoginComponent,
    canActivate: [GuestGuard],
    children: [
      {
        path: '',
        loadChildren: () =>
          import('./login/login.module').then((m) => m.LoginModule),
      },
    ],
  },
  {
    path: 'forgot-password',
    component: ForgotPasswordComponent,
    canActivate: [GuestGuard],
    children: [
      {
        path: '',
        loadChildren: () =>
          import('./forgot-password/forgot-password.module').then(
            (m) => m.ForgotPasswordModule
          ),
      },
    ],
  },
  {
    path: 'reset-password/:reset_token',
    component: ResetPasswordComponent,
    canActivate: [GuestGuard],
    children: [
      {
        path: '',
        loadChildren: () =>
          import('./reset-password/reset-password.module').then(
            (m) => m.ResetPasswordModule
          ),
      },
    ],
  },
  {
    path: 'register',
    component: RegisterComponent,
    canActivate: [GuestGuard],
    children: [
      {
        path: '',
        loadChildren: () =>
          import('./register/register.module').then((m) => m.RegisterModule),
      },
    ],
  },
  {
    path: '',
    component: LayoutComponent,
    canActivate: [AuthGuard],
    children: [
      {
        path: '',
        loadChildren: () =>
          import('./dashboard/dashboard.module').then((m) => m.DashboardModule),
      },
      {
        path: 'users',
        loadChildren: () =>
          import('./user/user.module').then((m) => m.UserModule),
      },
      {
        path: 'edit-profile',
        loadChildren: () =>
          import('./edit-profile/edit-profile.module').then(
            (m) => m.EditProfileModule
          ),
      },
    ],
  },
];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule],
})
export class LayoutRoutingModule {}
