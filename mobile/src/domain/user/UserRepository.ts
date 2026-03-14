import { User } from "./User"

type WithLoginCredentials<T> = T & {
  email: string
  password: string
}

export type UserLoginData = WithLoginCredentials<{ device: string }>
export type UserRegisterData = WithLoginCredentials<{ name: string }>

export interface UserRepository {
  user: () => Promise<User>
  login: (data: UserLoginData) => Promise<string>
  register: (data: UserRegisterData) => Promise<string>
  logout: () => Promise<void>
}
