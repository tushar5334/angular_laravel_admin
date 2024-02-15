export interface IUserAddEdit {
  user_id?: string;
  name: string;
  email: string;
  password?: string;
  password_confirmation?: string;
  user_type: string;
  status: number | boolean;
  profile_picture?: string;
  display_picture?: string;
  filesList: any;
}
