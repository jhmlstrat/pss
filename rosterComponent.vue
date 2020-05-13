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
      <h1><center>{{ year() }} {{ team.team_name }} before game {{game }}</center></h1>
      <b-row>
        <b-col cols='12' class='text-right'>
          <b-button v-show='team.team_name != "guest"'
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
                               v-bind:variant="playerVariant(player.player)"
                               group='roster' 
                               v-bind:key='player.player.name'>
              <b-row>
                <b-col cols='9' md='10'>
                  {{ player.player.name }}
                </b-col>
                <b-col cols='3' md='2'>
                  <b-form-select v-model='selected[player.player.name]' 
                                 class='pl-0 pt-0 pb-0'
                                 v-bind:options='choices(player.player.name)' 
                                 v-on:change="move(player.player.name)">
                  </b-form-select>
                </b-col>
              </b-row>
            </b-list-group-item>
          </b-list-group>
        </b-col>
        <b-col cols='6'>
          <h3 v-if='mod_minors.length == 0 || mod_minors[0].player.name == "-"'><center>Minors</center></h3>
          <h3 v-else><center>Minors ({{ mod_minors.length }})</center></h3>
            <b-list-group-item v-for='player in mod_minors' 
                               class='roster-item' 
                               v-bind:variant="playerVariant(player.player)"
                               group='roster'
                               v-bind:key='"MIN:"+player.player.name'>
              {{ player.player.name }}
            </b-list-group-item>
          <h3 class='pt-3' v-if='mod_il.length == 0 || mod_il[0].player.name == "-"'><center>Injured List</center></h3>
          <h3 class='pt-3' v-else><center>Injured List ({{ mod_il.length }})</center></h3>
            <b-list-group-item v-for='player in mod_il'
                               class='roster-item'
                               v-bind:variant="playerVariant(player.player)"
                               group='roster'
                               v-bind:key='"IL:"+player.player.name'>
              {{ player.player.name }}
            </b-list-group-item>
        </b-col>
      </b-row>
      <b-row class='float-right'>
        <b-col cols='12'>
          <b-button v-on:click='onSubmit' variant='primary' class='mr-4' v-bind:disabled='! rosterValid()'>Submit</b-button>
          <b-button v-on:click='onReset' variant='danger' class='mr-4'>Reset</b-button>
        </b-col>
      </b-row>
    </div>
  `,
  data() {
    return {
      game: 1,
      team: {"team_name":""},
      teamname: 't',
      mod_majors: [],
      mod_minors: [],
      mod_il: [],
      moves: [],
      selected: [{'':null}],
    }
  },
  watch: {
    mod_minors() {
      if (this.mod_minors.length == 0) this.mod_minors.push({'player':{'name':'-'}});
      else if (this.mod_minors.length > 1) {
        pos = -1;
        for (i=0; i< this.mod_minors.length; i++) {
          if (this.mod_minors[i].player.name == '-') pos = i;
        }
        if (pos > -1) this.mod_minors.splice(pos,1);
      }
    }, 
    mod_il() {
      if (this.mod_il.length == 0) this.mod_il.push({'player':{'name':'-'}});
      else if (this.mod_il.length > 1) {
        pos = -1;
        for (i=0; i< this.mod_il.length; i++) {
          if (this.mod_il[i].player.name == '-') pos = i;
        }
        if (pos > -1) this.mod_il.splice(pos,1);
      }
    }, 
  },
  mounted() {
    eBus.$on('teamUpdated',(t) => { this.team = t;});
    eBus.$on('rosterUpdated',(r) => { this.rosterUpdated();})
    eBus.$on('gameUpdated',(g) => { this.game=g;})
    if (this.teamname == 't' && vue != undefined) vue.emitData();
  },
  methods: {
    rosterUpdated() {
      if (vue == undefined) return;
      if (vue.roster == undefined || vue.roster == null) return;
      this.reset();
    },
    rosterValid() {
      if (vue == undefined) return false;
      if (JSON.stringify(vue.roster) == '{}') return false;
      if (JSON.stringify(vue.schedule) == '{}') return false;
      if (vue.schedule.results.length > 83 && ((this.mod_minors.length == 1  && this.mod_minors[0].player.name == '-') || this.mod_majors.length == 40)) return true;
      if (this.mod_majors.length <= vue.roster_size) return true;
      return false;
    },
    currentComponent() { if (vue == undefined) return ''; return vue.currentComponent; },
    year() { if (vue == undefined) return ''; return vue.year; },
    reset(){
      this.moves=[];
      this.mod_majors = vue.majors.slice();
      this.mod_minors = vue.minors.slice();
      this.mod_il = vue.il.slice();
      for (p of vue.majors) {
        this.selected[p.player.name] = null;
      }
      for (p of vue.minors) {
        this.selected[p.player.name] = null;
      }
      for (p of vue.il) {
        this.selected[p.player.name] = null;
      }
      if (this.mod_minors.length == 0) this.mod_minors.push({'player':{'name':'-'}});
      if (this.mod_il.length == 0) this.mod_il.push({'player':{'name':'-'}});
    },
    canMove(name) {
      let where=this.whereIs(name);
      let ri=this.getRI(name);
      let lm=vue.lastMove(ri);
      let gSeries=vue.seriesGame(vue.gameNumber);
      let mSeries=vue.seriesGame(lm.gameNumber);
      if (where.start != where.current) return false;
      if (lm.lastMove == 'Traded') return false;
      if (lm.lastMove == 'Traded for') return true;
      if (mSeries == 0 && lm.lastMove != 'To DL') return true;
      if (vue.gameNumber > 83 && lm.lastMove != 'To DL') return true;
      if (gSeries - mSeries >= 3 && lm.lastMove != 'To DL') return true
      if (vue.gameNumber - lm.gameNumber >= vue.il_length && lm.lastMove == 'To DL') return true;
      if (vue.il != this.mod_il) return true;
        //case "Fm minors":  return 0;
        //case "To minors":  return 1;
        //case "On DL":      return 2;
        //case "Off DL":     return 3;
        //case "Traded":     return 4;
        //case "Traded for": return 5;
      return false;
    },
    isInjured(name) {
      let ri=this.getRI(name);
      let li=vue.lastInjury(ri);
      return (! (vue.day - li.lastInjury > li.duration));
    },
    choices(name) {
      rtn = [{ value: null, text: '' }];
      let where=this.whereIs(name);
      if (where.start == where.current) {
        if (where.start == 'majors') {
          if (this.canMove(name)) rtn.push({ value: 'minors', text: 'To Minors'});
          if (this.isInjured(name)) rtn.push({ value: 'il', text: 'To IL'})
        } else if (where.start == 'minors' ) {
          if (this.canMove(name)) rtn.push({ value: 'majors', text: 'To Majors'});
        } else {
          if (this.canMove(name)) rtn.push({ value: 'majors', text: 'To Majors'});
        }
      }
      return rtn;
    },
    whereIs(name) {
      let start = '';
      let current = '';
      for (p of vue.majors) {
        if (p.player.name == name) start = 'majors';
      }
      for (p of vue.minors) {
        if (p.player.name == name) start = 'minors';
      }
      for (p of vue.il) {
        if (p.player.name == name) start = 'il';
      }
      for (p of this.mod_majors) {
        if (p.player.name == name) current = 'majors';
      }
      for (p of this.mod_minors) {
        if (p.player.name == name) current = 'minors';
      }
      for (p of this.mod_il) {
        if (p.player.name == name) current = 'il';
      }
      return {'start':start,'current':current};
    },
    getRI(name) {
      let r = {};
      for (p of vue.majors) {
        if (p.player.name == name) r = p;
      }
      for (p of vue.minors) {
        if (p.player.name == name) r = p;
      }
      for (p of vue.il) {
        if (p.player.name == name) r = p;
      }
      return r;
    },
    move(name) {
      if (this.selected[name] == null) return;
      let where=this.whereIs(name);
      if (where.start == this.selected[name]) return;
      let ri=this.getRI(name);
      let found = false;
      if (where.start == 'majors') {
        for (let i=0; ! found && i<this.mod_majors.length; i++) {
          if (this.mod_majors[i].player.name == name) {
            this.mod_majors.splice(i,1);
            found = true;
          }
        }
      } else if (where.start == 'minors' ) {
        for (let i=0; ! found && i<this.mod_minors.length; i++) {
          if (this.mod_minors[i].player.name == name) {
            this.mod_minors.splice(i,1);
            found = true;
          }
        }
      } else {
        for (let i=0; ! found && i<this.mod_il.length; i++) {
          if (this.mod_il[i].player.name == name) {
            this.mod_il.splice(i,1);
            found = true;
          }
        }
      }
      found = false;
      m='';
      if (this.selected[name] == 'majors') {
        for (let i=0; ! found && i<this.mod_majors.length; i++) {
          if (((ri.player.strat.ab != 0 || this.mod_majors[i].player.strat.ab == 0) && this.mod_majors[i].player.name > name) ||
               (ri.player.strat.ab != 0 && this.mod_majors[i].player.strat != undefined && this.mod_majors[i].player.strat.ab == 0)){
            this.mod_majors.splice(i,0,ri);
            found = true;
          }
        }
        if (! found) this.mod_majors.push(ri);
        if (where.start == 'minors' ) m='Fm minors';
        else m='Off DL';
      } else if (this.selected[name] == 'minors' ) {
        for (let i=0; ! found && i<this.mod_minors.length; i++) {
          if (((ri.player.strat.ab != 0 || this.mod_minors[i].player.strat.ab == 0) && this.mod_minors[i].player.name > name) ||
               (ri.player.strat.ab != 0 && this.mod_minors[i].player.strat != undefined && this.mod_minors[i].player.strat.ab == 0)){
            this.mod_minors.splice(i,0,ri);
            found = true;
          }
        }
        if (! found) this.mod_minors.push(ri);
        m='To minors';
      } else {
        for (let i=0; ! found && i<this.mod_il.length; i++) {
          if (((ri.player.strat.ab != 0 || this.mod_il[i].player.strat.ab == 0) && this.mod_il[i].player.name > name) ||
               (ri.player.strat.ab != 0 && this.mod_il[i].player.strat != undefined && this.mod_il[i].player.strat.ab == 0)){
            this.mod_il.splice(i,0,ri);
            found = true;
          }
        }
        if (! found) this.mod_il.push(ri);
        m='To DL';
      }
      this.selected[name] = null;
      this.moves.push({'name':name,'moveType':m});
      //console.log(this.moves);
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
      return JSON.stringify(vue.rotation) == '{}';
    },
    onSubmit(evt) {
      evt.preventDefault();
      var self = this;
      let headers = {headers:{'X-Authorization':'TooManyMLs'}};
      axios.put('/pss/api/putRosterMoves.php',{data: {'year':vue.year,'team':self.team.team,'game':vue.game,'moves':this.moves}},headers)
      .then(function (response) {
        //console.log(response);
        vue.loadRoster();
        self.switchToMenu();
      })
      .catch(function (error) {
        console.error(error);
        self.switchToMenu();
      });
    },
    onReset(evt) {
      evt.preventDefault();
      this.reset();
    },
    playerVariant(p) {
      if (p.name == '-') return 'light';
      let ri=this.getRI(p.name);
      let lm=vue.lastMove(ri);
      if (lm.lastMove == 'To DL' && !(this.canMove(p.name))) return 'dark';
      if (this.isInjured(p.name)) return 'danger';
      if (p.strat.ab == 0) return 'info';
      return 'secondary';
    }
  },
};

