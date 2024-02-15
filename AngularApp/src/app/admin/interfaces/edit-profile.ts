export interface IEditProfile {
  name: string;
  email: string;
  password?: string;
  password_confirmation?: string;
  profile_picture?: string;
  display_picture?: string;
  filesList: any;
}
