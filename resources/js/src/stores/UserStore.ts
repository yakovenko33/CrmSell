
import { defineStore } from 'pinia'
import {UserInfo} from "../types/userInfo";

export const useUserStore = defineStore("UserStore", {
    state: () => {
        return {
            token: localStorage.getItem('x_xsrf_token') || null,
            userInfo: null as UserInfo|null,
        }
    },
    getters: {
        isAuthenticated(): boolean {
            return this.token;
        },
        getUserInfo(): UserInfo|null {
            return this.userInfo;
        },
    },
    actions: {
        login(token: string): void {
            localStorage.setItem('x_xsrf_token', token);
            this.token = token;
        },
        setUser(user: UserInfo): void {
            this.userInfo = user;
        },
        logOut(): void {
            localStorage.removeItem('x_xsrf_token');
            this.token = null;
        },
    },
})
