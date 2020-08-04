var LineupComponent = {
  name: 'LineupComponent',
  template: `
    <div class='container'>
      <b-tabs>
        <b-tab title="Lineup">
<!--
{{ side }}
{{roster}}
{{rotation}}
-->

          <b-row class='mt-3'>
            <b-col cols='6'>
              <b-row>
                <b-col cols="1">
                </b-col>
                <b-col>
                  Opposing Pitcher: {{ ((!oside.rotation) || oside.rotation.length == 0) ? "Unassigned" : oside.rotation[oside.rotation.length - 1].name + '(' +  oPitchHand(oside.rotation[oside.rotation.length - 1].name) + ')'}}
                </b-col>
              </b-row>
              <b-row  v-for="(sB,index) in selectedB" v-bind:key="index" class="mt-1">
                <b-col cols="1">
                  {{ index + 1 }}
                </b-col>
                <b-col cols="9">
                  <b-form-select v-model="selectedB[index]" v-bind:options="batterSelections"></b-form-select>
                </b-col>
                <b-col cols="2">
                  <b-form-select v-model="selectedPos[index]" v-bind:options="posSelections"></b-form-select>
                </b-col>
              </b-row>
              <b-row v-show="errorString != ''" class="mt-3">
                <b-col cols="1">
                </b-col>
                <b-col>
                  <b-alert show variant="danger" v-html="errorString"></b-alert>
                </b-col>
              </b-row>
              <b-row v-show="warnString != ''" class="mt-3">
                <b-col cols="1">
                </b-col>
                <b-col>
                  <b-alert show variant="warning" v-html="warnString"></b-alert>
                </b-col>
              </b-row>
              <b-button v-on:click='onSubmitB' variant='primary' class='mt-3 mr-3 float-right' v-show="lineupValid() && ! submittedB">Submit</b-button>
            </b-col>
            <b-col cols='6'>
              <b-row class='mb-2'>
                <b-col cols='12'>
                  <span class="h5">Available:</span>
                </b-col>
              </b-row>
              <b-table striped hover small outlined bordered v-bind:items="availableBatters" v-bind:fields="bfields">
                <template v-slot:cell(hand)="data">
                  {{ data.item.strat.hand }}
                </template>
                <template v-slot:cell(fielding)="data">
                  {{ formatPositions(data.item.strat.positionsPlayed) }}
                </template>
              </b-table>
            </b-col>
          </b-row>
        </b-tab>
        <b-tab title="Rotation" v-bind:active="side.rotation && side.rotation.length == 0">
          <b-row class='mt-3'>
            <b-col cols='6'>
              <!-- Insert previous pitchers -->
              <b-table hover small v-bind:items="l_rotation" v-bind:fields="rfields" thead-class="d-none">
              </b-table>
              <b-form-select v-model='selectedP' v-bind:options='pitcherSelections' v-show="! submittedP"></b-form-select>
              <b-button v-on:click='onSubmitP' variant='primary' class='mt-3 mr-3 float-right' v-show="! submittedP && selectedP!= ''">Submit</b-button>
            </b-col>
            <b-col cols='6'>
              <b-row class='mb-2'>
                <b-col cols='12'>
                  <span class="h5">Available:</span>
                </b-col>
              </b-row>
              <b-table striped hover small outlined bordered v-bind:items="availablePitchers" v-bind:fields="pfields">
                <template v-slot:cell(hand)="data">
                  {{ data.item.strat.hand }}
                </template>
                <template v-slot:cell(endurance)="data">
                  {{ data.item.strat.endurance }}
                </template>
              </b-table>
            </b-col>
          </b-row>
<!--
{{oside}}
{{oroster}}
{{orotation}}
-->
        </b-tab>
      </b-tabs>
    </div>
  `,
  props: ['side','oside','roster','oroster','rotation','orotation'],
  data: function() {
    return {
      team: {'team_name':''},
      teamname: 't',
      config: {},
      availableBatters: [],
      batterSelections: [],
      selectedB: [],
      selectedPos: [],
      availablePitchers: [],
      pitcherSelections: [],
      posSelections: [{'value':'P','text':'P'},
                      {'value':'C','text':'C'},
                      {'value':'1B','text':'1B'},
                      {'value':'2B','text':'2B'},
                      {'value':'3B','text':'3B'},
                      {'value':'SS','text':'SS'},
                      {'value':'LF','text':'LF'},
                      {'value':'CF','text':'CF'},
                      {'value':'RF','text':'RF'},
                      {'value':'DH','text':'DH'}],
      selectedB: ['','','','','','','','',''],
      selectedPos: ['','','','','','','','',''],
      selectedP: '',
      submittedB: false,
      submittedP: false,
      bfields: [
        'name',
        'hand',
        'fielding',
      ],
      pfields: [
        'name',
        'hand',
        'endurance',
      ],
      rfields: [
        'name',
      ],
      l_lineup: [[],[],[],[],[],[],[],[],[]],
      l_rotation: [],
      errorString: '',
      warnString: '',
    }
  },
  watch: {
    side() {
      this.setAvailableBatters();
      this.setAvailablePitchers();
    },
  },
  mounted() {
    eBus.$on('configUpdated',(c) => { this.config = vue.cyConfig;});
    eBus.$on('teamUpdated',(t) => { this.team = t;});
  },
  methods: {
    lineupValid() {
      let pos=[false,false,false,false,false,false,false,false,false];
      dupePlayer = false;
      dupePos = false;
      missingPlayer = false;
      missingPos = false;
      errorString = "";
      warnString = "";
      assigned = [];
      for (let i = 0; i < this.selectedB.length; i++) {
        b = this.selectedB[i];
        if (b == '') {
          missingPlayer = true;
          continue;
        }
        if (assigned.includes(b)) { dupePlayer = true; }
        else { assigned.push(b); }
        if (this.selectedPos[i] == '') continue;
        p = this.selectedPos[i];
        for (a of this.roster.roster.batters) {
          if (a.rosterItem.player.name == b) {
            oop = true;
            for (ps of a.rosterItem.player.strat.positionsPlayed) {
              if (ps.position.pos == p) oop = false;
            }
            if (oop) warnString = 'Player out of position';
          }
        }
      }
      for (p of this.selectedPos) {
        if (p == '') continue;
        if (p == 'P' || p == 'DH') { pc = 0; }
        if (p == 'C') { pc = 1; }
        if (p == '1B') { pc = 2; }
        if (p == '2B') { pc = 3; }
        if (p == '3B') { pc = 4; }
        if (p == 'SS') { pc = 5; }
        if (p == 'LF') { pc = 6; }
        if (p == 'CF') { pc = 7; }
        if (p == 'RF') { pc = 8; }
        if (pos[pc]) { dupePos = true; }
        else { pos[pc] = true; }
      }
      for (p of pos) if (! p) missingPos = true;
      if (dupePlayer) {
        errorString += 'Duplicate players assigned';
      }
      if (dupePos) {
        if (errorString != '') errorString += '<br/>';
        errorString += 'Duplicate positions assigned';
      }
      if (missingPlayer) {
        if (errorString != '') errorString += '<br/>';
        errorString += 'Not all players assigned';
      }
      if (missingPos) {
        if (errorString != '') errorString += '<br/>';
        errorString += 'Not all positions assigned';
      }
      this.errorString = errorString;
      this.warnString = warnString;
      if (dupePlayer || dupePos || missingPlayer || missingPos) return false;
      return true;
    },
    setAvailableBatters() {
      this.availableBatters = [];
      this.batterSelections=[{'value':'','text':''}];
      for (b of this.roster.roster.batters) {
        if (b.rosterItem.moves.length > 0 && b.rosterItem.moves[b.rosterItem.moves.length -1].move.moveType == 'To minors') continue;
        if (b.rosterItem.moves.length > 0 && b.rosterItem.moves[b.rosterItem.moves.length -1].move.moveType == 'On DL') continue;
        if (b.rosterItem.moves.length > 0 && b.rosterItem.moves[b.rosterItem.moves.length -1].move.moveType == 'Traded') continue;
        if (b.rosterItem.moves.length > 0 && b.rosterItem.moves[b.rosterItem.moves.length -1].move.moveType == 'Traded for') continue;
        let inLineup = false;
        for (l of this.side.lineup) {
          for (lu of l) {
            if (lu.name == b.rosterItem.player.name) inLineup=true;
          }
        }
        if (inLineup) continue;
        this.availableBatters.push(b.rosterItem.player);
        this.batterSelections.push({'value':b.rosterItem.player.name,'text':b.rosterItem.player.name});
      }
    },
    setAvailablePitchers() {
      if (! this.side.rotation) return [];
      for (p of this.side.rotation) {
        this.l_rotation.push({'name':p.name});
      }
      this.availablePitchers = [];
      starters = [];
      sr = [];
      relievers = [];
      for (p of this.roster.roster.pitchers) {
        if (p.rosterItem.moves.length > 0 && p.rosterItem.moves[p.rosterItem.moves.length -1].move.moveType == 'To minors') continue;
        if (p.rosterItem.moves.length > 0 && p.rosterItem.moves[p.rosterItem.moves.length -1].move.moveType == 'On DL') continue;
        if (p.rosterItem.moves.length > 0 && p.rosterItem.moves[p.rosterItem.moves.length -1].move.moveType == 'Traded') continue;
        if (p.rosterItem.moves.length > 0 && p.rosterItem.moves[p.rosterItem.moves.length -1].move.moveType == 'Traded for') continue;
        used = false;
        for (r of this.side.rotation) {
          if (r.name == p.rosterItem.player.name) used = true;
        }
        if (used) continue;
        s = p.rosterItem.player.strat.endurance.includes('S');
        r = p.rosterItem.player.strat.endurance.includes('R');
        p.rosterItem.player.tired = false;
        p.rosterItem.player.injured = false;
        if (s && r) sr.push(p.rosterItem.player);
        else if (s) starters.push(p.rosterItem.player);
        else relievers.push(p.rosterItem.player);
      }
      if (this.side.rotation.length == 0) this.availablePitchers = starters.concat(sr.concat(relievers));
      else this.availablePitchers = relievers.concat(sr.concat(starters));
      this.pitcherSelections = [];
      for (p of this.availablePitchers) {
        if (this.side.rotation.length == 0 && this.rotation.rotation.rotation[parseInt(this.side.gameNumber) - 1].pitcher == p.name) this.selectedP = p.name;
        this.pitcherSelections.push({'value':p.name,'text':p.name});
      }
    },
    formatPositions(pos) {
      rtn = '';
      arm = 0;
      for (p of pos) {
        if (rtn != '') rtn += ', ';
        ppos = p.position;
        if (ppos.pos == 'C') {
          rtn += ppos.pos + '-' + ppos.rating + '(' + ppos.arm + ')e' + ppos.e;
        } else if (ppos.pos == 'B1' || ppos.pos == 'B2' || ppos.pos == 'B3') {
          rtn += ppos.pos.slice(1) + 'B-' + ppos.rating + 'e' + ppos.e;
        } else if (ppos.pos == 'LF' || ppos.pos == 'CF' || ppos.pos == 'RF') {
          rtn += ppos.pos + '-' + ppos.rating + '(' + ppos.arm + ')e' + ppos.e;
        } else {
          rtn += ppos.pos + '-' + ppos.rating + 'e' + ppos.e;
        }
      }
      return rtn;
    },
    onSubmitB() {
    },
    onSubmitP() {
      this.side.rotation.push({'name':this.selectedP});
      this.setAvailablePitchers();
      this.$emit('rotationUpdate',this.side.rotation);
      this.submittedP = true;
    },
    oPitchHand(pitch) {
      for (p of this.oroster.roster.pitchers) {
        if (p.rosterItem.player.name == pitch) {
          return p.rosterItem.player.strat.hand;
        }
      }
    }
  },
};

