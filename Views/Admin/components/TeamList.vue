<style>
    .team-list .list-accordion .tile-text{
        display: inline-block;
        width: 95%;
    }
    .team-list .list-accordion .tile-content{
        display: inline-block;
        padding: 16px;
        vertical-align: top;
        width: 5%;
    }

</style>

<template>
    <div class="team-list">

        <div class="team-list-container">
            <div class="card-head style-primary">
                <header><i class="fa fa-users"></i> Équipe</header>
                <div class="tools">
                    <a @click="updateOrCreate" class="btn btn-default-bright"><i class="fa fa-save"></i> Enregistrer</a>
                </div>
            </div>
            <div class="card-body no-padding">
                <!-- BEGIN RESULT LIST -->
                <div class="list-results list-results-underlined">
                    <ul class="list list-accordion panel-group" id="team-accordion" data-sortable="true">
                        <member v-for="(member, key) in team" @memberDeleted="deleteMember" :key="key"
                                :reload_roles="reload_roles" :id="key" :member="member" :roles="roles"
                                :website_id="website_id"></member>
                    </ul>
                    <button data-toggle="modal" @click="addMember"
                            class="btn btn-primary pull-right">
                        <i class="fa fa-plus" aria-hidden="true"></i>
                        Ajouter un collaborateur
                    </button>
                </div><!--end .list-results -->
                <!-- END RESULTS LIST -->
            </div>
        </div><!--end .section-body -->

    </div>

</template>


<script type="text/babel">

    import {mapActions} from 'vuex'
    import {team_api} from '../api'

    export default
    {
        name: 'team-list',
        components: {
            Member: resolve => {
                require(['./Member.vue'], resolve)
            }
        },
        props: {
            website_id: {
                required: true
            },
            team: {
                type: Array,
                default: () => {
                    return []
                }
            },
            roles: {
                type: Array,
                default: () => {
                    return []
                }
            },
            reload_roles: {
                type: Boolean,
                default: false
            }
        },
        methods: {
            ...mapActions(['update']),
            addMember(){
                this.team.push({
                    id: 'create-' + this.team.length,
                    full_name: 'Membre ' + this.team.length,
                    description: '',
                    gender: 0,
                    position: this.team.length,
                    roles: [],
                    photo: {
                        path: '',
                        alt: ''
                    }
                });
            },
            deleteMember(id){
                let index = this.team.findIndex((i) => i.id == id);
                this.team.splice(index, 1);
            },
            updateOrCreate(){
                this.update({
                    api: team_api.update_or_create + this.website_id,
                    value: {
                        team: this.team
                    }
                }).then((response) => {
                    if (response.data.resource !== undefined) {
                        response.data.resource.forEach((member) => {
                            let index = this.team.findIndex((i) => i.position == member.position);
                            this.team[index]['id'] = member.id;
                        })
                    }
                });
            }
        },
        mounted () {
            let o = this;
            $('#team-accordion').sortable({
                placeholder: "ui-state-highlight",
                delay: 100,
                start: function (e, ui) {
                    ui.placeholder.height(ui.item.outerHeight() - 1);
                },
                stop: function (event, ui) {
                    let new_postions = [];
                    $('#' + ui.item[0].parentNode.id + ' > li').each((index, li) => {
                        let id = $(li).attr('data-id');
                        let i = o.team.findIndex((i) => i.id == id);
                        new_postions[i] = index;
                    });
                    new_postions.forEach((element, index) => {
                        o.team[index].position = element;
                    })
                }
            });
        }
    }
</script>
