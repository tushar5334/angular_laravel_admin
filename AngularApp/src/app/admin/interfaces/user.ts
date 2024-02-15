export interface IUser {
  user_id?: string;
  name: string;
  email: string;
  password: string;
  user_type: string;
  status: number | boolean;
  profile_picture: string;
  token: string;
  is_selected: boolean;
  created_by: string;
  updated_by: string;
  created_at: Date;
}
