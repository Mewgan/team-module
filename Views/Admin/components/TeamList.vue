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
                    <a @click="save" class="btn btn-default"><i class="fa fa-save"></i> Enregistrer</a>
                </div>
            </div>
            <div class="card-body no-padding">
                <!-- BEGIN RESULT LIST -->
                <div class="list-results list-results-underlined">
                    <ul class="list list-accordion panel-group" id="team-accordion" data-sortable="true">
                        <member v-for="(member, key) in team" :key="key" :index="key" :member="member" :roles="roles" :website_id="website_id"></member>
                    </ul>
                    <button data-toggle="modal" @click="addMember"
                            class="btn ink-reaction btn-raised btn-lg btn-info pull-right">
                        <i class="fa fa-plus" aria-hidden="true"></i>
                        Ajouter un collaborateur
                    </button>
                </div><!--end .list-results -->
                <!-- END RESULTS LIST -->
            </div>
        </div><!--end .section-body -->


        <div class="modal fade" id="deleteMemberModal" tabindex="-1" role="dialog" aria-labelledby="simpleModalLabel"
             aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                        <h4 class="modal-title" id="deleteMemberModalLabel">Suppression</h4>
                    </div>
                    <div class="modal-body">
                        <p>Êtes-vous sûr de vouloir supprimer ce membre de votre équipe ?</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Non</button>
                        <button type="button" class="btn btn-primary" data-dismiss="modal">Oui
                        </button>
                    </div>
                </div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->
        </div>
    </div>

</template>


<script type="text/babel">

    import '@admin/libs/jquery-ui/jquery-ui.min'

    import {mapActions} from 'vuex'
    import {team_api} from '../api'

    export default
    {
        name: 'team-list',
        components: {
            Member: resolve => { require(['./Member.vue'], resolve) },
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
            }
        },
        data () {
            return {
                selected_items: []
            }
        },
        methods: {
            ...mapActions(['update']),
            addMember(){
                this.team.push({
                    full_name: 'Membre ' + this.team.length,
                    description: '',
                    gender: 0,
                    position: this.team.length,
                    roles: [],
                    photo: {
                        path: '/public/media/user/default-photo.png',
                        alt: ''
                    }
                });
            },
            save(){
                this.update({
                    api: team_api.update_or_create + this.website_id,
                    value: {
                        team: this.team
                    }
                }).then((response) => {
                    if(response.data.resource !== undefined)
                        this.$emit('teamUpdated', response.data.resource);
                });
            }
        },
        mounted () {
            let o = this;
            $('[data-sortable="true"]').sortable({
                placeholder: "ui-state-highlight",
                delay: 100,
                start: function (e, ui) {
                    ui.placeholder.height(ui.item.outerHeight() - 1);
                },
                stop: function (event, ui) {
                    let new_postions = [];
                    $('#' + ui.item[0].parentNode.id + ' > li').each((index, li) => {
                        let name = $(li).attr('data-name');
                        let i = o.team.findIndex((i) => i.full_name == name);
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
