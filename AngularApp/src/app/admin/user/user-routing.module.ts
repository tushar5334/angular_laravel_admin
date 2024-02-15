import { NgModule } from '@angular/core';
import { RouterModule, Routes } from '@angular/router';
import { UserListComponent } from './user-list/user-list.component';
import { UserAddEditComponent } from './user-add-edit/user-add-edit.component';
const routes: Routes = [
  { path: '', component: UserListComponent },
  { path: 'add-user', component: UserAddEditComponent },
  { path: 'edit-user/:user_id', component: UserAddEditComponent },
];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule],
})
export class UserRoutingModule {}
