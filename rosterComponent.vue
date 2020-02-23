var RosterComponent = {
  name: 'RosterComponent',
  //components: { draggable },
  template: `
    <div class='container'>
{{ currentComponent() }}
      <b-row>
        <b-col cols='2' class='text-center'>
          <b-button variant='link' size='lg' href='#' v-on:click='switchToMenu();'>
            Pss Menu
          </b-button>
        </b-col>
        <b-col cols='8'>
        </b-col>
        <b-col cols='2' class='text-center'>
          <b-button variant='link' size='lg' href='/jhmlhome.html'>
            JHML Home
          </b-button>
        </b-col>
      </b-row>
      <h1><center>{{ team.team_name }} before game {{game }}</center></h1>
      <b-row>
        <b-col cols='12' class='text-right'>
          <b-button v-show='teamname != "guest"'
                    variant='primary'
                    size='lg'
                    href='#' 
                    v-on:click='switchToRotation();' 
                    v-bind:disabled='rotationNotLoaded()'>
            Rotation
          </b-button>
        </b-col>
      </b-row>
      <b-row>
        <b-col cols='6'>
          <h3><center>Majors ({{ mod_majors.length }}) - {{ rosterValid() ? 'Valid' : 'INVALID' }} </center></h3>
          <b-list-group>
            <b-list-group-item v-for='player in mod_majors' 
                               class='roster-list'
                               group='roster' 
                               v-bind:key='player.player.name'>
              <b-row>
                <b-col cols='9' md='10'>
                  {{ player.player.name }}
                </b-col>
                <b-col cols='3' md='2'>
                  <b-form-select v-model='selected' 
                                 class='pl-0 pt-0 pb-0'
                                 v-bind:options='majorsOptions' 
                                 v-bind:change="move(player.player.name,selected)">
                  </b-form-select>
                </b-col>
              </b-row>
            </b-list-group-item>
          </b-list-group>
        </b-col>
        <b-col cols='6'>
          <h3 v-if='mod_minors[0].player.name == "-"'><center>Minors</center></h3>
          <h3 v-else><center>Minors ({{ mod_minors.length }})</center></h3>
            <b-list-group-item cless='roster-item' v-for='player in mod_minors' grnup='roster' v-bind:key='player.player.name'>{{ player.player.name }} </b-list-group-item>
          <h3 class='mt-5'><center>Injured List</center></h3>
            <b-list-group-item cless='roster-item' v-for='player in mod_il' grnup='roster' v-bind:key='player.player.name'>{{ player.player.name }} </b-list-group-item>
        </b-col>
      </b-row>
      <b-row class='float-right'>
        <b-col cols='12'>
          <b-button v-on:click='onSubmit' variant='primary' class='mr-4'>Submit</b-button>
          <b-button v-on:click='onReset' variant='danger' class='mr-4'>Reset</b-button>
        </b-col>
      </b-row>
    {{majors}}    
<br>Roster: {{ roster }}
    </div>
  `,
  data() {
    return {
      games: [],
      game: 1,
      team: {},
      teamname: 't',
      config: {},
      roster: {},
      rotation: {},
      majors: [],
      mod_majors: [],
      minors: [],
      mod_minors: [],
      moves: [],
      il: [],
      mod_il: [],
      selected: null,
      majorsOptions: [
        { value: null, text: '' },
        { value: 'minors', text: 'To Minors'},
      ],
    }
  },
  watch: {
    roster() {
      if (this.roster == undefined || this.roster == null) return;
      console.log(this.roster.roster);
      this.majors = [];
      this.minors = [];
      this.il = [];
      if (this.roster.roster.batters)
        for (b of this.roster.roster.batters) {
          console.log(b)
          this.majors.push(b.rosterItem);
        }
      if (this.roster.roster.pitchers)
        for (b of this.roster.roster.pitchers) {
          this.majors.push(b.rosterItem);
        }
      this.reset();
    },
    mod_minors() {
      if (this.mod_minors.length == 0) this.mod_minors.push({'name':'-'});
      else if (this.mod_minors.length > 1) {
        pos = -1;
        for (i=0; i< this.mod_minors.length; i++) {
          if (this.mod_minors[i].name == '-') pos = i;
        }
        if (pos > -1) this.mod_minors.splice(pos,1);
      }
    }, 
    mod_il() {
      if (this.mod_il.length == 0) this.mod_il.push({'name':'-'});
      else if (this.mod_il.length > 1) {
        pos = -1;
        for (i=0; i< this.mod_il.length; i++) {
          if (this.mod_il[i].name == '-') pos = i;
        }
        if (pos > -1) this.mod_il.splice(pos,1);
      }
    }, 
    config() {
      this.setNames();
    },
    teamname() {
      this.setNames();
    },
  },
  mounted() {
    eBus.$on('configUpdated',(c) => { this.config=c; })
    eBus.$on('teamnameUpdated',(tn) => { this.teamname=tn; })
    eBus.$on('rosterUpdated',(r) => { this.roster=r;})
    eBus.$on('rotationUpdated',(r) => { this.rotation=r;})
    eBus.$on('gameUpdated',(g) => { this.game=g;})
    if (this.teamname == 't' && vue != undefined) vue.emitData();
  },
  methods: {
    changes(evt) {
      console.log(evt);
      console.log(this.mod_majors);
      console.log(this.mod_minors);
      console.log(this.mod_il);
    },
    rosterValid() {
      if (JSON.stringify(this.roster) == '{}') return false;
      if (this.games.length > 83 && this.mod_minors.length == 1  && this.mod_minors[0].player.name == '-') return true;
      if (this.mod_majors.length <= 25) return true;
      return false;
    },
    currentComponent() { return vue.currentComponent; },
    setNames() {
      if (this.teamname == 't') return;
      if (! this.config.current_year) return;
      for (y of this.config.years) {
        if (y.year == this.config.current_year) {
          for (t of y.teams) {
            if (t.team == this.teamname) this.team = t;
          }
        }
      }
    },
    reset(){
      this.moves=[];
      this.mod_majors = this.majors.slice();
      this.mod_minors = this.minors.slice();
      this.mod_il = this.il.slice();
      if (this.mod_minors.length == 0) this.mod_minors.push({'player':{'name':'-'}});
      if (this.mod_il.length == 0) this.mod_il.push({'player':{'name':'_'}});
    },
    move(name,moveType) {
      this.selected='';
      console.log(name);
      console.log(moveType);
    },
    switchToMenu() {
      vue.currentComponent='menuComponent';
      vue.emitData();
    },
    switchToRotation() {
      vue.currentComponent='rotationComponent';
      vue.emitData();
    },
    rotationNotLoaded() {
      return JSON.stringify(this.rotation) == '{}';
    },
    onSubmit(evt) {
      evt.preventDefault();
    },
    onReset(evt) {
      evt.preventDefault();
      this.mod_majors = this.majors.slice(0);
      this.mod_minors = this.minors.slice(0);
      this.mod_il = this.il.slice(0);
    },
  },
};

