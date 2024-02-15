import { Component, OnInit, OnDestroy } from '@angular/core';
import { IMenu } from 'src/app/admin/interfaces/menu';
import { AuthService } from 'src/app/admin/services/auth.service';
import { Subscription } from 'rxjs';
@Component({
  selector: 'app-sidebar',
  templateUrl: './sidebar.component.html',
  styleUrls: ['./sidebar.component.scss'],
})
export class SidebarComponent implements OnInit, OnDestroy {
  private userDataListnerSub!: Subscription;
  userData: any;
  menuList: IMenu[] = [
    {
      text: 'Dashboard',
      icon: 'dashboard',
      routerLink: '/admin',
    },
    /* {
      text: 'User',
      icon: 'people',
      routerLink: 'users',
    }, */
    {
      text: 'User',
      icon: 'people',
      children: [
        {
          text: 'Users',
          icon: 'list',
          routerLink: 'users',
        },
        {
          text: 'Add User',
          icon: 'add_box',
          routerLink: 'users/add-user',
        },
      ],
    },
    /* {
      text: 'Supplier',
      icon: 'supervised_user_circle',
      routerLink: '/supplier/manage',
    },
    {
      text: 'Suit',
      icon: 'inventory_2',
      children: [
        {
          text: 'Category',
          icon: 'category',
          routerLink: '/product/category',
        },
        {
          text: 'Sub Category',
          icon: 'layers',
          routerLink: '/product/sub-category',
        },
        {
          text: 'Product',
          icon: 'all_inbox',
          routerLink: '/product/manage',
        },
      ],
    },
    {
      text: 'Expense',
      icon: 'inventory_2',
      children: [
        {
          text: 'Category',
          icon: 'category',
          routerLink: '/product/category',
        },
        {
          text: 'Manage Expense',
          icon: 'layers',
          routerLink: '/product/sub-category',
        },
        {
          text: 'Statement',
          icon: 'all_inbox',
          routerLink: '/product/manage',
        },
      ],
    },
    {
      text: 'Purchases',
      icon: 'receipt',
      children: [
        {
          text: 'New Purchases',
          icon: 'local_atm',
          routerLink: '/purchases/new',
        },
        {
          text: 'Purchases History',
          icon: 'history',
          routerLink: '/purchases/history',
        },
      ],
    },
    {
      text: 'Sales',
      icon: 'calculate',
      children: [
        {
          text: 'New Sales',
          icon: 'point_of_sale',
          routerLink: '/sales/add',
        },
        {
          text: 'Sales History',
          icon: 'history',
          routerLink: '/sales/history',
        },
      ],
    },
    {
      text: 'Report',
      icon: 'analytics',
      routerLink: '/reports',
    }, */
  ];

  constructor(private authService: AuthService) {
    let userData: string =
      this.authService.getItemFromLocalStorage('user') || '{}';
    this.userData = JSON.parse(userData);
  }

  ngOnInit(): void {
    this.userDataListnerSub = this.authService.userDataListner.subscribe({
      next: (data: any) => {
        this.userData = data;
      },
      error: (error: any) => {
        console.log('error userDataListnerSub');
      },
    });
  }

  trackByFn(index: number, item: any) {
    return index; // unique id corresponding to the item
  }

  ngOnDestroy() {
    this.userDataListnerSub.unsubscribe();
  }
}
