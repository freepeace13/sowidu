export interface PasswordRepository {
  confirmPassword: (password: string) => Promise<boolean>
}
