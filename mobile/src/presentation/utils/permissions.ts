import { Role } from "@domain/teams/permissions/Role"

export function isFounder(role: Role) {
  return role.name === "Founder"
}

export function withoutFounder(role: Role) {
  return !isFounder(role)
}
