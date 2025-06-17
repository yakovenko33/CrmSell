
import {initAuth} from "../auth.service";
import {RolesEnum} from "../enum/roles.enum";

const permissionMixin =  {
    data() {
        return {
            rolesEnum: RolesEnum,
        }
    },
    methods: {
        hasRoles(roles: string[]) {
            return initAuth().hasRoles(roles);
        },
        hasPermission(permission: string[]) {
            return initAuth().hasPermission(permission);
        }
    }
}

export default permissionMixin;
