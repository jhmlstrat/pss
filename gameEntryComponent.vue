var GameEntryComponent = {
  name: 'GameEntryComponent',
  template: `
    <div class="container">
      <b-row>
        <b-col cols="11">
        </b-col>
        <b-col cols="1" class="text-right">
          <b-button variant="link" href="#" v-on:click="doPlay('undo')" >Undo</b-button> 
        </b-col>
      </b-row>
      <b-row v-show="! rarePlay">
        <b-col cols="1" class="text-right" v-if="! plus">
          <b-button variant="link" href="#" v-on:click="doPlay('WP')" v-show="(occupied(1) || occupied(2) || occupied(3)) ||
                                                (plus && other == 'K' && empty(1) && empty(2) && empty(3))">WP</b-button> 
        </b-col>
        <b-col cols="1" class="text-right" v-else>
        </b-col>
        <b-col cols="1" class="text-right" v-if="! plus">
          <b-button variant="link" href="#" v-on:click="doPlay('B')" v-show="(occupied(1) || occupied(2) || occupied(3))">B</b-button> 
        </b-col>
        <b-col cols="1" class="text-right" v-else>
        </b-col>
        <b-col cols="1" class="text-right" v-if="! plus">
          <b-button variant="link" href="#" v-on:click="doPlay('PB')" v-show="(occupied(1) || occupied(2) || occupied(3)) ||
                                                (plus && other == 'K' && empty(1) && empty(2) && empty(3))">PB</b-button> 
        </b-col>
        <b-col cols="1" class="text-right" v-else>
        </b-col>
        <b-col cols="1" class="text-right" v-if="! plus">
        </b-col>
        <b-col cols="1" class="text-right" v-else>
        </b-col>
        <b-col cols="1" class="text-right" v-if="! plus">
          <b-button variant="link" href="#" v-on:click="doPlay('SB')" v-show="(occupied(1) || occupied(2) || occupied(3))">SB</b-button> 
        </b-col>
        <b-col cols="1" class="text-right" v-else>
        </b-col>
        <b-col cols="1" class="text-right" v-if="! plus">
          <b-button variant="link" href="#" v-on:click="doPlay('CS')" v-show="(occupied(1) || occupied(2) || occupied(3))">CS</b-button> 
        </b-col>
        <b-col cols="1" class="text-right" v-else>
        </b-col>
        <b-col cols="1" class="text-right" v-if="! plus">
          <b-button variant="link" href="#" v-on:click="doPlay('PO')" v-show="(occupied(1) || occupied(2) || occupied(3))">PO</b-button> 
        </b-col>
        <b-col cols="1" class="text-right" v-else>
        </b-col>
        <b-col cols="1" class="text-right" v-if="! plus">
          <b-button variant="link" href="#" v-on:click="doPlay('DI')" v-show="(occupied(1) || occupied(2) || occupied(3))">DI</b-button> 
        </b-col>
        <b-col cols="1" class="text-right" v-else>
        </b-col>
        <b-col cols="1" class="text-right">
        </b-col>
        <b-col cols="1" class="text-right">
        </b-col>
        <b-col cols="1" class="text-right">
          <b-button variant="link" href="#" v-on:click="doRarePlay()" v-show="! plus">RP</b-button>
          <b-button variant="link" href="#" v-on:click="showInjury()" v-show="plus">Injury</b-button>
        </b-col>
        <b-col cols="1" class="text-right">
          <b-button variant="link" href="#" v-on:click="doPlus()" v-show="! plus">+</b-button>
          <b-button variant="link" href="#" v-on:click="doMinus()" v-show="plus">-</b-button>
        </b-col>
      </b-row>
      <b-row v-show="rarePlay || plus">
        <b-col cols="1" class="text-right">
          <b-button v-b-tooltip.hover title="With runner on first base, line drive hits runner. Runner out, batter to first on a single, other runners hold" variant="link" href="#" v-on:click="showInfield('RP-S1')" v-if="rarePlay">Rare S1</b-button> 
          <b-button variant="link" href="#" v-on:click="doPlay('S1')" v-else>S1</b-button> 
        </b-col>
        <b-col cols="1" class="text-right">
          <b-button v-b-tooltip.hover title="Batter singles, runners advancing two bases. Fielder throws behind batter who is out at first" variant="link" href="#" v-on:click="doPlay('RP-S2')" v-if="rarePlay">Rare S2</b-button> 
          <b-button variant="link" href="#" v-on:click="doPlay('S2')" v-else>S2</b-button> 
        </b-col>
        <b-col cols="1" class="text-right">
          <b-button v-b-tooltip.hover title="Batter doubles, runners advancing two bases. Fielder throws behind batter who is out at second on the hidden ball play" variant="link" href="#" v-on:click="doPlay('RP-D2')" v-if="rarePlay">Rare D2</b-button> 
          <b-button variant="link" href="#" v-on:click="doPlay('D2')" v-else>D2</b-button> 
        </b-col>
        <b-col cols="1" class="text-right">
          <b-button v-b-tooltip.hover title="With no runners on, the batter hits a double, but is out on an appeal of missing first base" variant="link" href="#" v-on:click="showOutfield('RP-D3')" v-if="rarePlay">Rare D3</b-button> 
          <b-button variant="link" href="#" v-on:click="doPlay('D3')" v-else>D3</b-button> 
        </b-col>
        <b-col cols="1" class="text-right">
          <b-button v-b-tooltip.hover title="Fielders collide, inside the park home run" variant="link" href="#" v-on:click="doPlay('RP-T3')" v-if="rarePlay">Rare T3</b-button> 
          <b-button variant="link" href="#" v-on:click="doPlay('T3')" v-else>T3</b-button> 
        </b-col>
        <b-col cols="1" class="text-right">
          <b-button v-b-tooltip.hover title="With runner on first, runner breaks up the double play, but is called out interference. Runner and batter out - other runners do not advance.  No runner on first - batter out, runners all hold" variant="link" href="#" v-on:click="showInfield('RP-G1')" v-if="rarePlay">Rare G1</b-button> 
          <b-button variant="link" href="#" v-on:click="doPlay('G1')" v-else>G1</b-button> 
        </b-col>
        <b-col cols="1" class="text-right">
          <b-button v-b-tooltip.hover title="Batter hits a shot off the pitcher to the fielder. Batter out at first, runners advance only if forced" variant="link" href="#" v-on:click="showInfield('RP-G2')" v-if="rarePlay">Rare G2</b-button> 
          <b-button variant="link" href="#" v-on:click="doPlay('G2')" v-else>G2</b-button> 
        </b-col>
        <b-col cols="1" class="text-right">
          <b-button v-b-tooltip.hover title="Batter hits a shot off the mound. Batter out at first, runners advance 1 base (even with IF in)" variant="link" href="#" v-on:click="showInfield('RP-G3')" v-if="rarePlay">Rare G3</b-button> 
          <b-button variant="link" href="#" v-on:click="doPlay('G3')" v-else>G3</b-button> 
        </b-col>
        <b-col cols="1" class="text-right">
        </b-col>
        <b-col cols="1" class="text-right">
        </b-col>
        <b-col cols="1" class="text-right">
        </b-col>
        <b-col cols="1" class="text-right">
          <b-button variant="link" href="#" v-on:click="doRarePlay()" v-show="! plus">RP</b-button>
        </b-col>
        <b-col cols="1" class="text-right">
        </b-col>
      </b-row>
      <b-row v-show="rarePlay || plus">
        <b-col cols="1" class="text-right">
          <b-button v-b-tooltip.hover title="Outfielder makes the catch, but crashes into the wall and is dazed. Runners tag and advance 2 bases" variant="link" href="#" v-on:click="showOutfield('RP-F1')" v-if="rarePlay">Rare F1</b-button> 
          <b-button variant="link" href="#" v-on:click="doPlay('F1')" v-else>F1</b-button> 
        </b-col>
        <b-col cols="1" class="text-right">
          <b-button v-b-tooltip.hover title="With runner on 3rd, outfielder makes catch and runner is out on an appeal for leaving early" variant="link" href="#" v-on:click="showOutfield('RP-F2')" v-if="rarePlay">Rare F2</b-button> 
          <b-button variant="link" href="#" v-on:click="doPlay('F2')" v-else>F2</b-button> 
        </b-col>
        <b-col cols="1" class="text-right">
          <b-button v-b-tooltip.hover title="Outfielder makes a running catch and lead runner is unable to get back - double play" variant="link" href="#" v-on:click="showOutfield('RP-F3')" v-if="rarePlay">Rare F3</b-button> 
          <b-button variant="link" href="#" v-on:click="doPlay('F3')" v-else>F3</b-button> 
        </b-col>
        <b-col cols="1" class="text-right">
          <b-button v-b-tooltip.hover title="With runners on - WP that catcher cannot find, runners advance 2 bases. No runners on - K + WP, batter to first" variant="link" href="#" v-on:click="doPlay('RP-W/S')" v-if="rarePlay">Rare W/S</b-button> 
          <b-button variant="link" href="#" v-on:click="doPlay('W/S')" v-else>W/S</b-button> 
        </b-col>
        <b-col cols="1" class="text-right">
          <b-button v-b-tooltip.hover title="With runners on - WP, runners advance 1 base. No runners on - K + WP, batter to first" variant="link" href="#" v-on:click="doPlay('RP-W/G')" v-if="rarePlay">Rare W/G</b-button> 
          <b-button variant="link" href="#" v-on:click="doPlay('W/G')" v-else>W/G</b-button> 
        </b-col>
        <b-col cols="1" class="text-right">
          <b-button v-b-tooltip.hover title="Catcher's interference, batter to first, runners advance if forced." variant="link" href="#" v-on:click="doPlay('RP-P/F')" v-if="rarePlay">Rare P/F</b-button> 
          <b-button variant="link" href="#" v-on:click="doPlay('P/F')" v-else>P/F</b-button> 
        </b-col>
        <b-col cols="1" class="text-right">
          <b-button v-b-tooltip.hover title="With runners on - PB with runner on 3rd holding, other runners advance if possible. No runners on - K + PB, batter to first" variant="link" href="#" v-on:click="doPlay('RP-P/P')" v-if="rarePlay">Rare P/P</b-button> 
          <b-button variant="link" href="#" v-on:click="doPlay('P/P')" v-else>P/P</b-button> 
        </b-col>
        <b-col cols="1" class="text-right">
          <b-button v-b-tooltip.hover title="Infield popup no one takes charge of, drops for a Single*" variant="link" href="#" v-on:click="doPlay('RP-PO')" v-if="rarePlay">Rare PO</b-button> 
          <b-button variant="link" href="#" v-on:click="doPlay('PO')" v-else>PO</b-button> 
        </b-col>
          <b-button v-b-tooltip.hover title="With runner on first, catcher picks him off. No runner on first - fan interference" variant="link" href="#" v-on:click="doPlay('RP-FO')" v-if="rarePlay">Rare FO</b-button> 
          <b-button variant="link" href="#" v-on:click="doPlay('FO')" v-else>PO</b-button> 
        <b-col cols="1" class="text-right">
        </b-col>
        <b-col cols="1" class="text-right">
        </b-col>
        <b-col cols="1" class="text-right">
        </b-col>
        <b-col cols="1" class="text-right">
        </b-col>
      </b-row>
      <b-row v-show="!rarePlay">
        <b-col cols="12">
          <b-button variant="link" href="#" v-on:click="fielder='6';showInfield('gbA')">gbA</b-button> 
          <b-button variant="link" href="#" v-on:click="fielder='6';showInfield('gbB')">gbB</b-button> 
          <b-button variant="link" href="#" v-on:click="fielder='6';showInfield('gbC')">gbC</b-button> 
          <b-button variant="link" href="#" v-on:click="fielder='6';showInfield('gb!')" v-show="gameInfo.situation.situation.outs != 2">gb!</b-button> 
          <b-button variant="link" href="#" v-on:click="fielder='5';showInfield('SAC')" v-show="gameInfo.situation.situation.outs != 2">SAC</b-button> 
          <b-button variant="link" href="#" v-on:click="fielder='8';showOutfield('flyA')">flyA</b-button> 
          <b-button variant="link" href="#" v-on:click="fielder='8';showOutfield('flyB')">flyB</b-button> 
          <b-button variant="link" href="#" v-on:click="fielder='8';showOutfield('flyC')">flyC</b-button> 
          <b-button variant="link" href="#" v-on:click="fielder='8';showOutfield('fly!')" v-show="gameInfo.situation.situation.outs != 2">fly!</b-button> 
          <b-button variant="link" href="#" v-on:click="fielder='6';showInfield('lo')">lo</b-button> 
          <b-button variant="link" href="#" v-on:click="fielder='6';showInfield('lo!')" v-show="gameInfo.situation.situation.outs != 2">lo!</b-button> 
          <b-button variant="link" href="#" v-on:click="fielder='6';showInfield('po')">po</b-button> 
          <b-button variant="link" href="#" v-on:click="fielder='2';showInfield('fo')">fo</b-button> 
          <b-button variant="link" href="#" v-on:click="doPlay('K')">K</b-button> 
  
        </b-col>
      </b-row>
      <b-row v-show="!rarePlay">
        <b-col cols="12">
          <b-button variant="link" href="#" v-on:click="doPlay('S*')">S*</b-button> 
          <b-button variant="link" href="#" v-on:click="doPlay('S**')">S**</b-button> 
          <b-button variant="link" href="#" v-on:click="doPlay('S-dot')">S-dot</b-button> 
          <b-button variant="link" href="#" v-on:click="doPlay('S+')">S-plus</b-button> 
          <b-button variant="link" href="#" v-on:click="doPlay('S!')">S!</b-button> 
          <b-button variant="link" href="#" v-on:click="doPlay('BPS+')">BPS+</b-button> 
          <b-button variant="link" href="#" v-on:click="fielder='6';showInfield('BPS-')">BPS-</b-button>
          <b-button variant="link" href="#" v-on:click="doPlay('D**')">D**</b-button> 
          <b-button variant="link" href="#" v-on:click="doPlay('D***')">D***</b-button> 
          <b-button variant="link" href="#" v-on:click="doPlay('D!')">D!</b-button> 
          <b-button variant="link" href="#" v-on:click="doPlay('T')">T</b-button> 
          <b-button variant="link" href="#" v-on:click="doPlay('HR')">HR</b-button> 
          <b-button variant="link" href="#" v-on:click="doPlay('BPHR+')">BPHR+</b-button> 
          <b-button variant="link" href="#" v-on:click="fielder='8';showOutfield('BPHR-')">BPHR-</b-button> 
          <b-button variant="link" href="#" v-on:click="fielder='9';showOutfield('BPHR-!')" v-show="gameInfo.situation.situation.outs != 2">BPHR-!</b-button> 
          <b-button variant="link" href="#" v-on:click="doPlay('BB')">BB</b-button> 
          <b-button variant="link" href="#" v-on:click="doPlay('HBP')">HBP</b-button> 
          <b-button variant="link" href="#" v-on:click="showE()">E</b-button> 
<!--
          <b-button variant="link" href="#" v-on:click="doPlay('CI')">CI</b-button> 
-->
        </b-col>
      </b-row>
      <b-modal id="unusual-play" iref="modal" size="lg" title="Verify Runners" hide-header-close hide-footer>
        <template #default="{ ok }">
          <b-row>
            <b-col cols="12" class="text-center">
              {{ tmpPlay }} - {{ fielder }} 
            </b-col>
          </b-row>
          <b-row>
            <b-col cols="4">
            </b-col>
            <b-col cols="1">
              Out 1B
            </b-col>
            <b-col cols="1">
              Safe 1B
            </b-col>
            <b-col cols="1">
              Out 2B
            </b-col>
            <b-col cols="1">
              Safe 2B
            </b-col>
            <b-col cols="1">
              Out 3B
            </b-col>
            <b-col cols="1">
              Safe 3B
            </b-col>
            <b-col cols="1">
              Out Home
            </b-col>
            <b-col cols="1">
              Safe Home
            </b-col>
          </b-row>
          <b-row>
            <b-col cols="4">
              Batter: {{ gameInfo.situation.situation.batter }}
            </b-col>
            <b-col cols="1">
              <b-form-radio v-model="expected[0]" name="batter-running-radios" value="-1" v-show="showUnusualSpot(0,-1)"></b-form-radio>
            </b-col>
            <b-col cols="1">
              <b-form-radio v-model="expected[0]" name="batter-running-radios" value="1" v-show="showUnusualSpot(0,1)"></b-form-radio>
            </b-col>
            <b-col cols="1">
              <b-form-radio v-model="expected[0]" name="batter-running-radios" value="-2" v-show="showUnusualSpot(0,-2)"></b-form-radio>
            </b-col>
            <b-col cols="1">
              <b-form-radio v-model="expected[0]" name="batter-running-radios" value="2" v-show="showUnusualSpot(0,2)"></b-form-radio>
            </b-col>
            <b-col cols="1">
              <b-form-radio v-model="expected[0]" name="batter-running-radios" value="-3" v-show="showUnusualSpot(0,-3)"></b-form-radio>
            </b-col>
            <b-col cols="1">
              <b-form-radio v-model="expected[0]" name="batter-running-radios" value="3" v-show="showUnusualSpot(0,3)"></b-form-radio>
            </b-col>
            <b-col cols="1">
              <b-form-radio v-model="expected[0]" name="batter-running-radios" value="-4" v-show="showUnusualSpot(0,-4)"></b-form-radio>
            </b-col>
            <b-col cols="1">
              <b-form-radio v-model="expected[0]" name="batter-running-radios" value="4" v-show="showUnusualSpot(0,4)"></b-form-radio>
            </b-col>
          </b-row>
          <b-row v-show="occupied(1)">
            <b-col cols="4">
              First: {{ gameInfo.situation.situation.first }}
            </b-col>
            <b-col cols="1">
              <b-form-radio v-model="expected[1]" name="batter-running-radios" value="-1" v-show="showUnusualSpot(1,-1)"></b-form-radio>
            </b-col>
            <b-col cols="1">
              <b-form-radio v-model="expected[1]" name="batter-running-radios" value="1" v-show="showUnusualSpot(1,1)"></b-form-radio>
            </b-col>
            <b-col cols="1">
              <b-form-radio v-model="expected[1]" name="batter-running-radios" value="-2" v-show="showUnusualSpot(1,-2)"></b-form-radio>
            </b-col>
            <b-col cols="1">
              <b-form-radio v-model="expected[1]" name="batter-running-radios" value="2" v-show="showUnusualSpot(1,2)"></b-form-radio>
            </b-col>
            <b-col cols="1">
              <b-form-radio v-model="expected[1]" name="batter-running-radios" value="-3" v-show="showUnusualSpot(1,-3)"></b-form-radio>
            </b-col>
            <b-col cols="1">
              <b-form-radio v-model="expected[1]" name="batter-running-radios" value="3" v-show="showUnusualSpot(1,3)"></b-form-radio>
            </b-col>
            <b-col cols="1">
              <b-form-radio v-model="expected[1]" name="batter-running-radios" value="-4" v-show="showUnusualSpot(1,-4)"></b-form-radio>
            </b-col>
            <b-col cols="1">
              <b-form-radio v-model="expected[1]" name="batter-running-radios" value="4" v-show="showUnusualSpot(1,4)"></b-form-radio>
            </b-col>
          </b-row>
          <b-row v-show="occupied(2)">
            <b-col cols="4">
              Second: {{ gameInfo.situation.situation.second }}
            </b-col>
            <b-col cols="1">
            </b-col>
            <b-col cols="1">
            </b-col>
            <b-col cols="1">
              <b-form-radio v-model="expected[2]" name="batter-running-radios" value="-2" v-show="showUnusualSpot(2,-2)"></b-form-radio>
            </b-col>
            <b-col cols="1">
              <b-form-radio v-model="expected[2]" name="batter-running-radios" value="2" v-show="showUnusualSpot(2,2)"></b-form-radio>
            </b-col>
            <b-col cols="1">
              <b-form-radio v-model="expected[2]" name="batter-running-radios" value="-3" v-show="showUnusualSpot(2,-3)"></b-form-radio>
            </b-col>
            <b-col cols="1">
              <b-form-radio v-model="expected[2]" name="batter-running-radios" value="3" v-show="showUnusualSpot(2,3)"></b-form-radio>
            </b-col>
            <b-col cols="1">
              <b-form-radio v-model="expected[2]" name="batter-running-radios" value="-4" v-show="showUnusualSpot(2,-4)"></b-form-radio>
            </b-col>
            <b-col cols="1">
              <b-form-radio v-model="expected[2]" name="batter-running-radios" value="4" v-show="showUnusualSpot(2,4)"></b-form-radio>
            </b-col>
          </b-row>
          <b-row v-show="occupied(3)">
            <b-col cols="4">
              Third: {{ gameInfo.situation.situation.third }}
            </b-col>
            <b-col cols="1">
            </b-col>
            <b-col cols="1">
            </b-col>
            <b-col cols="1">
            </b-col>
            <b-col cols="1">
            </b-col>
            <b-col cols="1">
              <b-form-radio v-model="expected[3]" name="batter-running-radios" value="-3" v-show="showUnusualSpot(3,-3)"></b-form-radio>
            </b-col>
            <b-col cols="1">
              <b-form-radio v-model="expected[3]" name="batter-running-radios" value="3" v-show="showUnusualSpot(3,3)"></b-form-radio>
            </b-col>
            <b-col cols="1">
              <b-form-radio v-model="expected[3]" name="batter-running-radios" value="-4" v-show="showUnusualSpot(3,-4)"></b-form-radio>
            </b-col>
            <b-col cols="1">
              <b-form-radio v-model="expected[3]" name="batter-running-radios" value="4" v-show="showUnusualSpot(3,4)"></b-form-radio>
            </b-col>
          </b-row>
        </template>
      </b-modal>
      <b-modal id="outfield-position" ref="modal" size="md" title="Outfield Positioning" hide-header-close hide-footer>
        <template #default="{ ok }">
          <b-row>
            <b-col cols="6">
              <b-form-group id="outfield-positions" label="Fielder?">
                <b-form-radio v-model="fielder" name="outfield-fielder-radios" class="pl-5" value="7">Left</b-form-radio>
                <b-form-radio v-model="fielder" name="outfield-fielder-radios" class="pl-5" value="8">Center</b-form-radio>
                <b-form-radio v-model="fielder" name="outfield-fielder-radios" class="pl-5" value="9">Right</b-form-radio>
              </b-form-group>
            </b-col>
            <b-col cols="6">
            </b-col>
          </b-row>
          <hr class="pl-0 pr-0 w-100">
          <b-button size="sm" class="float-right" variant="success" @click="handleOutfield()">
            OK
          </b-button>
        </template>
      </b-modal>
      <b-modal id="infield-position" ref="modal" size="md" title="Infield Positioning" hide-header-close hide-footer>
        <template #default="{ ok }">
          <b-row>
            <b-col cols="6">
              <b-form-group id="infield-positions" label="Fielder?">
                <b-form-radio v-model="fielder" name="infield-fielder-radios" class="pl-5" value="1" v-show="tmpPlay != 'fo' && tmpPlay != 'BPS-'">Pitcher</b-form-radio>
                <b-form-radio v-model="fielder" name="infield-fielder-radios" class="pl-5" value="2" v-show="! (tmpPlay.startsWith('lo') || tmpPlay == 'BPS-')">Catcher</b-form-radio>
                <b-form-radio v-model="fielder" name="infield-fielder-radios" class="pl-5" value="3" v-show="tmpPlay != 'BPS-'">First</b-form-radio>
                <b-form-radio v-model="fielder" name="infield-fielder-radios" class="pl-5" value="4" v-show="! (tmpPlay == 'fo' || tmpPlay == 'SAC')">Second</b-form-radio>
                <b-form-radio v-model="fielder" name="infield-fielder-radios" class="pl-5" value="5" v-show="tmpPlay != 'BPS-'">Third</b-form-radio>
                <b-form-radio v-model="fielder" name="infield-fielder-radios" class="pl-5" value="6" v-show="! (tmpPlay == 'fo' || tmpPlay == 'SAC')">Shortstop</b-form-radio>
              </b-form-group>
            </b-col>
            <b-col cols="6">
              <b-form-group id="in-back" label="Infield?" v-show="occupied(3)"> 
                <b-form-radio v-model="infield" name="infield-position-radios" class="pl-5" value="back">Back</b-form-radio>
                <b-form-radio v-model="infield" name="infield-position-radios" class="pl-5" value="in">In</b-form-radio>
              </b-form-group>
            </b-col>
          </b-row>
          <hr class="pl-0 pr-0 w-100">
          <b-button size="sm" class="float-right" variant="success" @click="handleInfield()">
            OK
          </b-button>
        </template>
      </b-modal>
      <b-modal id="errors" ref="modal" size="md" :title="plus ? tmpPlay+' + Error' : 'Error'" hide-header-close hide-footer>
        <template #default="{ ok }">
          <b-row>
            <b-col cols="6">
              <b-form-group id="error-positions" label="Fielder?">
                <b-form-radio v-model="fielder" name="error-fielder-radios" class="pl-5" value="1" v-show="tmpPlay != 'S2' && tmpPlay != 'D2' && tmpPlay != 'D3' && tmpPlay != 'T3' && tmpPlay != 'F1' && tmpPlay != 'F2' && tmpPlay != 'F3'">Pitcher</b-form-radio>
                <b-form-radio v-model="fielder" name="error-fielder-radios" class="pl-5" value="2" v-show="tmpPlay != 'S2' && tmpPlay != 'D2' && tmpPlay != 'D3' && tmpPlay != 'T3' && tmpPlay != 'F1' && tmpPlay != 'F2' && tmpPlay != 'F3'">Catcher</b-form-radio>
                <b-form-radio v-model="fielder" name="error-fielder-radios" class="pl-5" value="3" v-show="tmpPlay == 'E' || tmpPlay == 'S1' || tmpPlay == 'S2' || tmpPlay == 'G1' || tmpPlay == 'G2' || tmpPlay == 'G3'">First</b-form-radio>
                <b-form-radio v-model="fielder" name="error-fielder-radios" class="pl-5" value="4" v-show="tmpPlay == 'E' || tmpPlay == 'S1' || tmpPlay == 'S2' || tmpPlay == 'G1' || tmpPlay == 'G2' || tmpPlay == 'G3'">Second</b-form-radio>
                <b-form-radio v-model="fielder" name="error-fielder-radios" class="pl-5" value="5" v-show="tmpPlay == 'E' || tmpPlay == 'S1' || tmpPlay == 'S2' || tmpPlay == 'G1' || tmpPlay == 'G2' || tmpPlay == 'G3'">Third</b-form-radio>
                <b-form-radio v-model="fielder" name="error-fielder-radios" class="pl-5" value="6" v-show="tmpPlay == 'E' || tmpPlay == 'S1' || tmpPlay == 'S2' || tmpPlay == 'G1' || tmpPlay == 'G2' || tmpPlay == 'G3'">Shortstop</b-form-radio>
                <b-form-radio v-model="fielder" name="error-fielder-radios" class="pl-5" value="7" v-show="tmpPlay == 'E' || tmpPlay == 'S2' || tmpPlay == 'D2' || tmpPlay == 'D3' || tmpPlay == 'T3' || tmpPlay == 'F1' || tmpPlay == 'F2' || tmpPlay == 'F3'">Left</b-form-radio>
                <b-form-radio v-model="fielder" name="error-fielder-radios" class="pl-5" value="8" v-show="tmpPlay == 'E' || tmpPlay == 'S2' || tmpPlay == 'D2' || tmpPlay == 'D3' || tmpPlay == 'T3' || tmpPlay == 'F1' || tmpPlay == 'F2' || tmpPlay == 'F3'">Center</b-form-radio>
                <b-form-radio v-model="fielder" name="error-fielder-radios" class="pl-5" value="9" v-show="tmpPlay == 'E' || tmpPlay == 'S2' || tmpPlay == 'D2' || tmpPlay == 'D3' || tmpPlay == 'T3' || tmpPlay == 'F1' || tmpPlay == 'F2' || tmpPlay == 'F3'">Right</b-form-radio>
              </b-form-group>
            </b-col>
            <b-col cols="6">
              <b-form-group id="error-bases" label="Bases?"> 
                <b-form-radio v-model="eBases" name="error-bases-radios" class="pl-5" value="1">0</b-form-radio>
                <b-form-radio v-model="eBases" name="error-bases-radios" class="pl-5" value="1">1</b-form-radio>
                <b-form-radio v-model="eBases" name="error-bases-radios" class="pl-5" value="2">2</b-form-radio>
                <b-form-radio v-model="eBases" name="error-bases-radios" class="pl-5" value="3" v-show="fielder == 7 || fielder == 8 || fielder == 9">3</b-form-radio>
              </b-form-group>
            </b-col>
          </b-row>
          <hr class="pl-0 pr-0 w-100">
          <b-button size="sm" class="float-right" variant="success" @click="handleE()">
            OK
          </b-button>
        </template>
      </b-modal>
      <b-modal id="injury" ref="modal" size="md" title="Injury" hide-header-close hide-footer>
TBD
      </b-modal>
<!--
    {{ gameInfo }}   
-->
    </div>
  `,
  props: ['gameInfo'],
  data: function() {
    return {
      plus: false,
      rarePlay: false,
      other: '',
      infield: 'back',
      fielder: '1',
      tmpPlay: '',
      eBases: 0,
      expected: [0,0,0,0],
    }
  },
  watch: {
  },
  mounted() {
  },
  methods: {
    currentComponent() { if (vue == undefined) return ''; return vue.currentComponent; },
    doMinus() {
      this.plus = false;
    },
    doPlay(play) {
      console.log(play);
      addBat=0;
      addFirst=0;
      addSecond=0;
      addThird=0;
      after='';
      injury = false;
      error = false;
      needModal = false;
      if (this.plus && this.other == "") {
        this.other = play;
        return;
      }
      sit = this.gameInfo.situation.situation;
      //console.log(sit);
      if (this.plus) {
        if (play == 'E' || this.other == 'E') {
          if (play == 'E') play == this.other;
          error = true;
        } else if (play == 'Injury' || this.other == 'Injury') {
          if (this.other == 'Injury') this.other == play;
          injury = true;
          this.plus = false;
        } else {
          console.log('Data entry - + with 2 nonsense choices');
          this.plus = false;
          return; //HUH?
        }
        this.plus = false;
      }
      if (play == 'SB') {
//SB!?
        baseCount = 0;
        if (this.occupied(1)) { addFirst ++; baseCount++; }
        if (this.occupied(2)) { addSecond ++; baseCount++; }
        if (this.occupied(3)) { addThird ++; baseCount++; }
        if (baseCount == 1) needModal = false;
        else needModal = true;
      } else if (play == 'CS') {
//CS!?
        baseCount = 0;
        if (this.occupied(1)) { addFirst --; baseCount++; }
        if (this.occupied(2)) { addSecond --; baseCount++; }
        if (this.occupied(3)) { addThird --; baseCount++; }
        if (baseCount == 1) needModal = false;
        else needModal = true;
      } else if (play == 'PO') {
//PO!?
        baseCount = 0;
        if (this.occupied(1)) { addFirst --; baseCount++; }
        if (this.occupied(2)) { addSecond --; baseCount++; }
        if (this.occupied(3)) { addThird --; baseCount++; }
        if (baseCount == 1) needModal = false;
        else needModal = true;
      } else if (play == 'DI') {
//DI!?
        baseCount = 0;
        if (this.occupied(1)) { addFirst ++; baseCount++; }
        if (this.occupied(2)) { addSecond ++; baseCount++; }
        if (this.occupied(3)) { addThird ++; baseCount++; }
        if (baseCount == 1) needModal = false;
        else needModal = true;
      } else if (play == 'gb!') {
        if ((this.empty(1) && this.empty(2) && this.empty(3)) ||  // empty
            (sit.outs == 2)) { 
          play = "gbC";
        } else {
          needModal = true;
        }
      } else if (play == 'fly!') {
        if ((this.empty(1) && this.empty(2) && this.empty(3)) ||  // empty
            (this.occupied(1) && this.empty(2) && this.empty(3)) ||  // 1st
            (sit.outs == 2)) { 
          play = "flyC";
        } else {
          needModal = true;
        }
      } else if (play == 'lo!') {
        if ((this.empty(1) && this.empty(2) && this.empty(3)) ||  // empty
            (sit.outs == 2)) { 
          play = "lo";
        } else {
          needModal = true;
        }
      } else if (play == 'S!') {
        if ((this.empty(1) && this.empty(2) && this.empty(3)) ||  // empty
            (this.empty(1) && this.empty(2) && this.occupied(3))) {  // 3rd
          play = "S*";
        } else {
          needModal = true;
        }
      } else if (play == 'D!') {
        if ((this.empty(1) && this.empty(2) && this.empty(3)) ||  // empty
            (this.empty(1) && this.occupied(2) && this.empty(3)) ||  // 2nd
            (this.empty(1) && this.empty(2) && this.occupied(3)) ||  // 3rd
            (this.empty(1) && this.occupied(2) && this.occupied(3))) {  // 2nd & 3rd
          play = "D**";
        } else {
          needModal = true;
        }
      } else if (play == 'BPHR-!') {
        if ((this.empty(1) && this.empty(2) && this.empty(3)) ||  // empty
            (this.occupied(1) && this.empty(2) && this.empty(3)) ||  // 1st
            (this.empty(1) && this.empty(2) && this.occupied(3)) ||  // 3rd
            (this.occupied(1) && this.empty(2) && this.occupied(3)) ||  // 1st & 3rd
            (sit.outs == 2) || (this.fielder != '9')) { 
          play = "BPHR-";
        } else {
          needModal = true;     // ?? RF (2-3?) ??
        }
      }
      //console.log(play + " - " + addThird + " - " + addSecond + " - " + addFirst + " - " + addBat);
      if (needModal) {
        this.tmpPlay = play;
        this.showUnusualPlay();
      } else {
        var self = this;
        let headers = {headers:{'X-Authorization':'TooManyMLs'}};
        axios.put('/pss/api/updateGame.php',{data: {'year':vue.year,'team':vue.team.team,'game':vue.game,'play':play,'fielder':self.fielder,'infield':self.infield,'eBases':self.eBases}},headers)
        .then(function (response) {
          console.log(response);
          vue.loadGameInfo();
          if (injury) {
            showInjury();
          }
        })
        .catch(function (error) {
          console.error(error);
        });
        this.rarePlay = false;
        this.plus = false;
        this.infield = 'back';
        this.eBases = 0;
        this.tmpPlay = '';
        this.other = '';
      }
    },
    doPlus() {
      this.plus = true;
      this.otherPlay = "";
    },
    doRarePlay() {
      this.rarePlay = ! this.rarePlay;
    },
    showOutfield(play) {
      this.tmpPlay = play;
      this.$bvModal.show('outfield-position');
    },
    handleOutfield() {
      this.$bvModal.hide('outfield-position');
      this.doPlay(this.tmpPlay);
    },
    showInfield(play) {
      this.tmpPlay = play;
      this.$bvModal.show('infield-position');
    },
    handleInfield() {
      this.$bvModal.hide('infield-position');
      this.doPlay(this.tmpPlay);
    },
    showE() {
      this.eBases = 1;
      this.fielder = '2';
      if (this.plus && this.other == 'SB') {
        doPlay('SB');
      } else {
        if (! this.plus) this.tmpPlay='E';
        else this.tmpPlay = this.other;
        if (this.tmpPlay == 'S2') this.fielder=6;
        if (this.tmpPlay == 'D2' || this.tmpPlay == 'D3' || this.tmpPlay == 'T3' || this.tmpPlay == 'F1' || this.tmpPlay == 'F2' || this.tmpPlay == 'F3') this.fielder = 8;
        this.$bvModal.show('errors');
      }
    },
    handleE() {
      this.$bvModal.hide('errors');
      this.doPlay(this.tmpPlay);
    },
    showUnusualPlay() {
      this.expected[0] = 0;
      this.expected[1] = 0;
      this.expected[2] = 0;
      this.expected[3] = 0;
      if (this.tmpPlay == 'gb!') {
        // All except empty
        // Assume tried for lead runner and didn't get them
        this.expected(0) = 1;
        if (this.occupied(3)) {
          this.expected[3] = 4;
          if (this.occupied(2)) this.expected[2] = 3;
          if (this.occupied(1)) this.expected[2] = 2;
        } else if (this.occupied(2)) {
          this.expected[2] = 3;
          if (this.occupied(1)) this.expected[2] = 2;
        } else {
          this.expected[1] = 2;
        }
      } else if (this.tmpPlay == 'fly!') {
        // runner on 2nd and/or 3rd
        this.expected[0] = -1;
        if (this.occupied(1)) this.expected[1] = 1;
        // If fielder == 9, assume run scores and runner on 2nd advancesadvances
        if (this.fielder == 9) {
          if (this.occupied(3)) this.expected[3] = 4;
          this.expected[2] = 3;
        } else {
        // Assume lead runner out
          if (this.occupied(3)) this.expected[3] = -4;
          else this.expected[2] = -3;
        }
      } else if (this.tmpPlay == 'lo!') {
        // All except empty
        // Assume lomax and DP
        this.expected[0] = -1;
        if (this.occupied(3)) {
          this.expected[3] = -3;
          if (this.occupied(2)) this.expected[2] = 2;
          if (this.occupied(1)) this.expected[1] = 1;
        } else if (this.occupied(2)) {
          this.expected[2] = -2;
          if (this.occupied(1)) this.expected[1] = 1;
        } else {
          this.expected[1] = -1;
        }
      } else if (this.tmpPlay == 'S!') {
        // All except empty or 3rd only
        this.expected[0] = 1;
        if (this.occupied(3)) this.expected[3] = 4;
        if (this.occupied(2)) {
          this.expected[2] = -4;
          if (this.occupied(1)) this.expected[1] = 3;
        } else {
          this.expected[1] = -3;
        }
      } else if (this.tmpPlay == 'D!') {
        // Runner on 1st
        this.expected[0] = 2;
        if (this.occupied(3)) this.expected[3] = 4;
        if (this.occupied(2)) this.expected[2] = 4;
        this.expected[1] = -4;
      } else if (this.tmpPlay == 'BPHR-!') {
        // runner on 2nd
        this.expected[0] = -1;
        if (this.occupied(3)) this.expected[3] = 4;
        this.expected[2] = 3;
        if (this.occupied(1)) this.expected[1] = 1;
      } else {
        console.error ('Should NOT get here - showUnusualPlay');
        return;
      }
      this.$bvModal.show('unusual-play');
    },
    handleUnusualPlay() {
      this.expected[0] = 0;
      this.expected[1] = 0;
      this.expected[2] = 0;
      this.expected[3] = 0;
      if (this.tmpPlay == 'gb!') {
//TBD
        // All except empty
        // Assume tried for lead runner and didn't get them
      
      } else if (this.tmpPlay == 'fly!') {
//TBD
        // runner on 2nd and/or 3rd
        // If fielder == 9, assume run scores and runner on 2nd advancesadvances
        // Assume lead runner out
      } else if (this.tmpPlay == 'lo!') {
//TBD
        // All except empty
        // Assume lomax and DP
      } else if (this.tmpPlay == 'S!') {
//TBD
        // All except empty or 3rd only
      } else if (this.tmpPlay == 'D!') {
//TBD
        // Runner on 1st
      } else if (this.tmpPlay == 'BPHR-!') {
//TBD
        // runner on 2nd
      } else {
        console.error ('Should NOT get here - handleUnusualPlay');
        return;
      }
      this.$bvModal.hide('unusual-play');
//TBD
      this.rarePlay = false;
      this.plus = false;
      this.infield = 'back';
      this.eBases = 0;
      this.tmpPlay = '';
      this.other = '';
    },
    showInjury() {
      if (this.other == '') {
        this.$bvModal.show('injury');
      } else {
        doPlay('Injury');
      }
    },
    handleInjury() {
      this.$bvModal.hide('injury');
//TBD
      if (this.other != '') doPlay(this.other);
    },
    showUnusualSpot(row, value) {
      if (this.tmpPlay == 'gb!') {
        // All except empty
        if (row == 0) {
          if (value == -1 || value == 1) return true;
        } else if (row == 1) {
          if (value == -2 || value == 2)return true;
        } else if (row == 2) {
          if (this.occupied(1) && (value == -3 || value == 3)) return true;
          if (this.empty(1) && (value == -2 || value == 2 || value == -3 || value == 3)) return true;
        } else {
          if ((this.occupied(1) && this.occupied(2)) && (value == -4 || value == 4)) return true;
          if ((this.empty(1) || this.empty(2)) && (value == -3 || value == 3 || value == -4 || value == 4)) return true;
        }
      } else if (this.tmpPlay == 'fly!') {
        // runner on 2nd and/or 3rd
        if (row == 0) {
          if (value == -1) return true;
        } else if (row == 1) {
          if (this.occupied(3) && (value == -1 || value == 1 || value == -2 || value == 2)) return true;
          if (this.empty(3) && (value == -1 || value == 1)) return true;
        } else if (row == 2) {
          if (value == -2 || value == 2 || value == -3 || value == 3) return true;
        } else {
          if (value == -4 || value == 4) return true;
        }
      } else if (this.tmpPlay == 'lo!') {
        // All except empty
        if (row == 0) {
          if (value == -1) return true;
        } else if (row == 1) {
          if (value == -1 || value == 1) return true;
        } else if (row == 2) {
          if (value == -2 || value == 2) return true;
        } else {
          if (value == -3 || value == 3) return true;
        }
      } else if (this.tmpPlay == 'S!') {
        // All except empty or 3rd only
        if (row == 0) {
          if (value == 1 || value == -2 || value == 2) return true;
        } else if (row == 1) {
          if (value == -2 || value == 2 || value == -3 || value == 3) return true;
        } else if (row == 2) {
          if (value == -4 || value == 4) return true;
        } else {
          if (value == 4) return true;
        }
      } else if (this.tmpPlay == 'D!') {
        // Runner on 1st
        if (row == 0) {
          if (value == 2 || value == -3 || value == 3) return true;
        } else if (row == 1) {
          if (value == -4 || value == 4) return true;
        } else if (row == 2) {
          if (value == 4) return true;
        } else {
          if (value == 4) return true;
        }
      } else if (this.tmpPlay == 'BPHR-!') {
        // runner on 2nd
        if (row == 0) {
          if (value == -1) return true;
        } else if (row == 1) {
          if (value == 1) return true;
        } else if (row == 2) {
          if (value == 3 || value == -3) return true;
        } else {
          if (value == 4) return true;
        }
      }
      return false;
    },
    occupied(base) {
      if (base == 1 && this.gameInfo.situation.situation.first != '') return true;
      if (base == 2 && this.gameInfo.situation.situation.second != '') return true;
      if (base == 3 && this.gameInfo.situation.situation.third != '') return true;
      return false;
    },
    empty(base) { return ! this.occupied(base); },
  },
};
