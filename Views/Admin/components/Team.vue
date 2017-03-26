<style>
    .team-module .breadcrumb {
        display: inline-block;
    }
    .team-module button {
        margin-left: 10px;
    }
    .team-module .section-header .btn{
        margin-left: 10px;
    }
</style>

<template>
    <section class="team-module">

        <div class="section-header">
            <ol class="breadcrumb">
                <li class="active">Équipe</li>
            </ol>
        </div>

        <div class="section-body">

            <div class="alert alert-info" role="alert">
                <strong><i class="fa fa-info-circle"></i> Gérer votre équipe</strong><br/>
            </div>

            <div class="col-md-4">
                <team-role-list :website_id="website_id" :roles="roles" @reloadTeam="reloadTeam"></team-role-list>
            </div>

            <div class="col-md-8">
                <team-list :website_id="website_id" :team="team" :roles="roles" :reload_roles="reload_roles"></team-list>
            </div><!--end .section-body -->

        </div>

    </section>

</template>


<script type="text/babel">

    import {mapActions} from 'vuex'
    import {team_api, team_role_api} from '../api'

    export default
    {
        components: {
            TeamList: resolve => {
                require(['./TeamList.vue'], resolve)
            },
            TeamRoleList: resolve => {
                require(['./TeamRoleList.vue'], resolve)
            }
        },
        data () {
            return {
                website_id: this.$route.params.website_id,
                team: [],
                roles: [],
                reload_roles: false
            }
        },
        methods: {
            ...mapActions(['read']),
            reloadTeam(){
                this.loadTeam().then(() => {
                    this.reload_roles = !this.reload_roles;
                });
            },
            loadTeam(){
                return this.read({api: team_api.all + this.website_id}).then((response) => {
                    if (response.data.resource !== undefined) {
                        this.team = response.data.resource;
                        this.team.forEach((member, index) => {
                            let ids = [];
                            if(member.roles instanceof Object){
                                this.team[index].roles = $.map(member.roles, (value) => {
                                    return [value];
                                })
                            }
                            this.team[index].roles.forEach((role) => {
                                ids.push(role.id);
                            });
                            this.team[index].roles = ids;
                        })
                    }
                })
            },
            loadRoles(){
                this.read({api: team_role_api.all + this.website_id}).then((response) => {
                    if (response.data.resource !== undefined) {
                        this.roles = response.data.resource;
                    }
                });
            }
        },
        created() {
            this.loadRoles();
            this.loadTeam();
        }
    }
</script>
