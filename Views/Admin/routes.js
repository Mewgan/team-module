export var routes = [
    {
        path: 'team-list',
        name: 'module:team',
        component: resolve => {
            require(['./components/Team.vue'], resolve)
        }
    },
];

export var content_routes = {
    team: (resolve) => {
        require(['./components/Module/TeamModule.vue'], resolve)
    },
    userTeam: (resolve) => {
        require(['./components/Module/UserTeamModule.vue'], resolve)
    }
};

export default {
    routes,
    content_routes
}