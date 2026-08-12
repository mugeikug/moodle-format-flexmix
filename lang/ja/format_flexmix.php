<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Japanese strings for format_flexmix.
 *
 * @package     format_flexmix
 * @copyright   2026 Hiroki Maezawa
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['pluginname'] = 'ウィークリー・非定期回・トピック混在';
$string['section0name'] = '全般';
$string['sectionoutline'] = 'コースセクション';

// コア(course/format/classes/output/local/content/section/controlmenu.php 等)が前提としている
// 標準的なコースフォーマット用文字列。セクションが週/トピック/非定期回のいずれにもなり得るため、
// 汎用的な表現にしている。
$string['addsections'] = 'セクションを追加する';
$string['currentsection'] = 'このセクション';
$string['editsection'] = 'セクションを編集する';
$string['editsectionname'] = 'セクション名を編集する';
$string['deletesection'] = 'セクションを削除する';
$string['newsectionname'] = 'セクション {$a} の新しい名前';
$string['sectionname'] = 'セクション';
$string['hidefromothers'] = '非表示にする';
$string['showfromothers'] = '表示する';

$string['sectiontype'] = 'セクションの種別';
$string['sectiontype_help'] = 'このセクションの扱い方を選択してください。

* **週** — ウィークリーフォーマットのように日付が自動計算されます。日付は、それより前にある「週」種別のセクションの数だけを数えて決まるため、途中に「トピック」や「非定期回」のセクションを挿入しても、後続の週の日付はズレません。
* **トピック** — トピックフォーマットのように日付を持ちません。セクション名は自分で付けてください。
* **非定期回(補講・振替など)** — 通常の週次の流れに含まれない、単発のセクションです(例: 補講、月曜日の授業を木曜日に振り替える場合など)。必要であれば個別に日付を設定でき、セクション名も自分で付けられます。';
$string['sectiontype_week'] = '週(日付あり)';
$string['sectiontype_topic'] = 'トピック(日付なし)';
$string['sectiontype_adhoc'] = '非定期回(補講・振替など)';

$string['sectiondate'] = 'セクションの日付';
$string['sectiondate_help'] = '「週」種別の場合、自動計算された日付をこの値で上書きします。「非定期回」種別の場合、既定のセクション名に表示される日付になります。空欄のままにすると、週は自動計算日付、非定期回は日付なしになります。「トピック」種別では使用されません。';

$string['topicsection'] = 'トピック {$a}';
$string['adhocsection'] = '非定期回';

$string['privacy:metadata'] = '「ウィークリー・非定期回・トピック混在」コースフォーマットは、セクション構造に関するメタデータ(セクション種別と任意の日付)のみを保存し、個人データは保存しません。';
