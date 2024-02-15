import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';

import { UserRoutingModule } from './user-routing.module';
import { UserListComponent } from './user-list/user-list.component';
import { SharedModule } from 'src/app/shared/admin/shared.module';
import { UserAddEditComponent } from './user-add-edit/user-add-edit.component';

@NgModule({
  declarations: [UserListComponent, UserAddEditComponent],
  imports: [CommonModule, UserRoutingModule, SharedModule],
})
export class UserModule {}
