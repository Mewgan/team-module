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
                <team-role-list :website_id="website_id" :roles="roles" @roleDeleted="deleteRole" @roleCreated="addRole"></team-role-list>
            </div>

            <div class="col-md-8">
                <team-list :website_id="website_id" :team="team" :roles="roles" @teamUpdated="updateTeam"></team-list>
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
            TeamList: resolve => { require(['./TeamList.vue'], resolve) },
            TeamRoleList: resolve => { require(['./TeamRoleList.vue'], resolve) },
        },
        data () {
            return {
                website_id: this.$route.params.website_id,
                team: [],
                roles: []
            }
        },
        methods: {
            ...mapActions(['read']),
            addRole(role){
                this.roles.push(role);
            },
            deleteRole(id){
                let index = this.roles.findIndex((i) => i.id == id);
                this.roles.splice(index, 1);
            },
            updateTeam(team){
                this.team = team;
            }
        },
        created() {
            this.read({api: team_role_api.all + this.website_id}).then((response) => {
                if(response.data.resource !== undefined)
                    this.roles = response.data.resource;
            });

            this.read({api: team_api.all + this.website_id}).then((response) => {
                if(response.data.resource !== undefined)
                    this.team = response.data.resource;
            })
        }
    }
</script>
