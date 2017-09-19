-- phpMyAdmin SQL Dump
-- version 4.6.6
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Sep 19, 2017 at 03:15 PM
-- Server version: 5.7.19-0ubuntu0.16.04.1
-- PHP Version: 5.6.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `ada`
--

-- --------------------------------------------------------

--
-- Table structure for table `campaigns`
--

CREATE TABLE `campaigns` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_deleted` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

--
-- Dumping data for table `campaigns`
--

INSERT INTO `campaigns` (`id`, `name`, `description`, `is_deleted`, `created_at`, `updated_at`) VALUES
(1, 'Billy Paul', 'I see you sittin\' there all alone', 0, '2017-09-04 16:02:12', '2017-09-04 16:26:19'),
(2, 'Angular', 'A Javascript framework for web artisans ;)', 0, '2017-09-04 16:02:53', '2017-09-08 17:13:20'),
(4, 'Tests', 'Development tests', 0, '2017-09-04 16:27:24', '2017-09-14 11:08:02');

-- --------------------------------------------------------

--
-- Table structure for table `emails`
--

CREATE TABLE `emails` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `campaign_id` int(10) UNSIGNED NOT NULL,
  `sender` text COLLATE utf8mb4_unicode_ci,
  `reply_to_email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `subject` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `content` mediumtext COLLATE utf8mb4_unicode_ci,
  `recipients_num` int(11) NOT NULL DEFAULT '0',
  `is_deleted` tinyint(1) NOT NULL DEFAULT '0',
  `status` tinyint(4) NOT NULL DEFAULT '-2',
  `sent_at` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

--
-- Dumping data for table `emails`
--

INSERT INTO `emails` (`id`, `user_id`, `campaign_id`, `sender`, `reply_to_email`, `subject`, `content`, `recipients_num`, `is_deleted`, `status`, `sent_at`, `created_at`, `updated_at`) VALUES
(1, 1, 4, NULL, NULL, 'Testing Sends', '<table border=\"0\" width=\"612\" cellspacing=\"0\" cellpadding=\"0\" align=\"center\">\n<tbody>\n<tr>\n<td colspan=\"2\" valign=\"top\"><img style=\"display: block;\" src=\"http://care.email-newsletter.info/files/images/top_banner.gif\" alt=\"CARE\" usemap=\"#map1\" width=\"612\" height=\"145\" border=\"0\" /></td>\n</tr>\n<tr>\n<td style=\"background: #e4701e; height: 33px; color: #ffffff; line-height: 33px; padding-right: 33px; font-family: Arial, Helvetica, sans-serif; font-size: 16px; font-weight: bold;\" colspan=\"2\" align=\"right\" valign=\"top\">Inside CI: Jamesy Test <span style=\"color: #fdb928;\">| September 2017</span></td>\n</tr>\n<tr>\n<td colspan=\"2\" valign=\"top\"><img style=\"display: block;\" src=\"http://care.email-newsletter.info/files/images/below_header_two.gif\" alt=\"CARE in Emergencies\" width=\"612\" height=\"26\" /></td>\n</tr>\n<tr>\n<td style=\"background: #ffffff;\" valign=\"top\">\n<table border=\"0\" width=\"518\" cellspacing=\"0\" cellpadding=\"5\" align=\"center\">\n<tbody>\n<tr>\n<td style=\"line-height: 16px; font-family: Arial, Helvetica, sans-serif; font-size: 13px;\" colspan=\"2\" valign=\"top\">\n<h3 style=\"color: #cc6633; line-height: 26px; font-family: Arial, Helvetica, sans-serif; font-size: 22px;\">Dear Colleges,</h3>\n<p style=\"line-height: 12.0pt;\"><span style=\"font-size: 10.0pt;\">Recently, a number of internal planning and strategic meetings went into high gear and the level of excitement and expectation around the network is almost palpable: The newly welcomed members of the CARE International Supervisory Board met for the first time in Geneva and showed outstanding commitment to helping us progress the CARE 2020 vision; two Town Hall meetings were widely attended by colleagues via webex, giving us the opportunity to discuss the organisation&rsquo;s challenges and milestones towards this vision. </span></p>\n<p style=\"line-height: 12.0pt;\"><span style=\"font-size: 10.0pt;\">While all of this is motivating in and of itself, there is always a special, renewed energy when visiting the field to discuss change and improvements in the way we meet the future. The West Africa Regional Leadership Meeting in Abidjan, Cote d&rsquo;Ivoire was a welcome opportunity to witness the dynamics around impact, growth, and strategy. Collectively, we recognise that working out objectives for a new accountability framework and ensuring robust membership development are activities directly linked to delivering on our programme strategy on the ground. In Africa, as in many parts of the world, CARE&rsquo;s signature initiatives and approaches such as the Village Savings and Loans Associations have the potential to amplify gains in resilience, inclusive governance and women&rsquo;s voice, as evidenced by the visit to communities in the cocoa-producing regions of Cote d&rsquo;Ivoire.&nbsp;</span></p>\n<p style=\"line-height: 12.0pt;\"><span style=\"font-size: 10.0pt;\">And as we met in our various planning sessions, humanitarian crises raged on &ndash; a powerful reminder of our work at hand. We were encouraged by the sterling work of our CARE country teams and communication colleagues to try and alleviate the suffering wrought by Hurricane Matthew in Haiti and bring it to the world&rsquo;s attention.&nbsp; We did so while trying not to forget the many other places in need and on the brink of starvation, such as Yemen</span><span style=\"font-size: 10.0pt;\">&nbsp;as <a href=\"http://www.youtube.com/watch?v=npk7tfKyXok\">this video</a> shows. </span></p>\n<p style=\"line-height: 12.0pt;\"><span style=\"font-size: 10.0pt;\">In this newsletter, we also highlight CARE&rsquo;s agreement with the International Food Policy Research Institute, playing our part to help end world hunger by 2030, if not earlier. October 16 every year is World Food Day so it was a timely signing of the partnership agreement. Meeting the food challenge means tackling the unjust fact that, in most instances, hunger is caused by man-made actions, conflict, violence, bad governance and climate change. And as with so many other challenges, we can therefore only reach our goal, if we continue to address hunger&rsquo;s underlying, political causes. </span></p>\n<p style=\"line-height: 12.0pt;\"><span style=\"font-size: 10.0pt;\">With warm regards,&nbsp;</span></p>\n<p><strong>Wolfgang Jamann<br /> Secretary General/CEO CARE International</strong></p>\n</td>\n</tr>\n<tr>\n<td style=\"background: #ffffff; padding: 20px 0px;\" colspan=\"2\" valign=\"top\"><img style=\"display: block;\" src=\"http://care.email-newsletter.info/files/images/btm_footer_line.gif\" width=\"518\" height=\"7\" /></td>\n</tr>\n<tr>\n<td style=\"padding: 0px; line-height: 16px; font-family: Arial, Helvetica, sans-serif; font-size: 13px;\" colspan=\"2\" valign=\"top\">\n<h3 style=\"color: #cc6633; line-height: 20px; font-family: Arial,Helvetica,sans-serif; font-size: 18px; font-weight: normal;\"><strong>First Supervisory Board meeting paves the way to CARE\'s transformational journey</strong></h3>\n<p><img style=\"width: 518px; height: 261px;\" src=\"http://care.email-newsletter.info/files/images/April%202016/Oct%202016/Board%20-%20pictures_518x261.png\" alt=\"Board members\" /><br /> <span style=\"font-size: 12px;\"><em>From top left, clockwise: Louise Frechette, Chairperson; Paul Jansen; Namrata Kaul; Reki Moussa Hassan;&nbsp;Susan Liautaud; Arielle de Rothschild.</em></span></p>\n<p>The inaugural CI Supervisory Board meeting in Geneva at the end of September marked a pivotal moment with discussions covering a range of topics, and with decisions on rules of procedure and the Board&rsquo;s role, as well as inputting and strategically positioning around key shared priorities.<br /> <br /> <a href=\"http://care.email-newsletter.info/email/inside-ci-news-from-the-ci-secretariat-october-2016#first-supervisory-board-meeting\"><span style=\"color: #ff8c00;\"><strong>Read more</strong></span></a></p>\n</td>\n</tr>\n<tr>\n<td style=\"background: #ffffff; padding: 20px 0px;\" colspan=\"2\" valign=\"top\"><img style=\"display: block;\" src=\"http://care.email-newsletter.info/files/images/btm_footer_line.gif\" width=\"518\" height=\"7\" /></td>\n</tr>\n<tr>\n<td style=\"padding: 0px; line-height: 16px; font-family: Arial, Helvetica, sans-serif; font-size: 13px;\" colspan=\"2\" valign=\"top\">\n<p style=\"color: #cc6633; line-height: 20px; font-family: Arial,Helvetica,sans-serif; font-size: 18px; font-weight: normal;\"><strong>New partnership sees CARE and IFPRI join forces to end world hunger</strong></p>\n<p><img style=\"width: 518px; height: 346px; max-width: 518px;\" src=\"http://care.email-newsletter.info/files/images/April%202016/Oct%202016/IFPRI%20signature_518x346.jpg\" alt=\"IFPRI\" /><br /> <span style=\"font-size: 12px;\"><em><span style=\"font-family: arial,helvetica,sans-serif;\">Wolfgang Jamann and <span lang=\"EN-US\" style=\"line-height: 107%; mso-fareast-font-family: Calibri; mso-fareast-theme-font: minor-latin;\"><span style=\"color: #000000;\">Shenggen Fan, Director General of IFPRI, shake hands after signing the Partnership Agreement.</span></span></span></em></span></p>\n<p>CARE International and the International Food Policy Research Institute (IFPRI) are strengthening collaboration with the aim of achieving food security, improving nutrition, and ending poverty. This objective aligns fully with the missions of both institutions and to ensure its achievement, the proposed collaboration will maximise CARE&rsquo;s extensive network and on-the-ground experience as well as IFPRI&rsquo;s high-calibre research capabilities.</p>\n<p>On October 6, Wolfgang Jamann,&nbsp;who also serves on the IFPRI Leadership Council, attended the Memorandum of Understanding (MoU)-signing event on behalf of CARE, a timely way to mark the upcoming World Food Day on October 16.</p>\n<p><strong><a href=\"http://care.email-newsletter.info/email/inside-ci-news-from-the-ci-secretariat-october-2016#care-and-ifpri\">Read more</a> </strong></p>\n</td>\n</tr>\n<tr>\n<td style=\"background: #ffffff; padding: 20px 0px;\" colspan=\"2\" valign=\"top\"><img style=\"display: block;\" src=\"http://care.email-newsletter.info/files/images/btm_footer_line.gif\" width=\"518\" height=\"7\" /></td>\n</tr>\n<tr>\n<td style=\"padding: 0px; line-height: 16px; font-family: Arial, Helvetica, sans-serif; font-size: 13px;\" colspan=\"2\" valign=\"top\">\n<p style=\"color: #cc6633; line-height: 20px; font-family: Arial,Helvetica,sans-serif; font-size: 18px; font-weight: normal;\"><strong>CARE scaling up humanitarian response in Haiti </strong></p>\n<p><img style=\"width: 518px; height: 345px; max-width: 518px;\" src=\"http://care.email-newsletter.info/files/images/April%202016/Oct%202016/haiti%20food_518x345.jpg\" alt=\"haiti\" /></p>\n<p>Hurricane Matthew crept up on Haiti and picked up such strength that it dumped massive rainfall,&nbsp;and left thousands in need of emergency assistance.</p>\n<p>CARE was well prepared with pre-positioned relief supplies. Immediately, CARE teams started distributing food and clean water to over 3,700 people in evacuation shelters in Port-au-Prince, the southeast and in Grande Anse Departments, where the brunt of the storm hit, with plans to to provide tarps and hygiene kits to some 50,000 people.</p>\n<p><strong><a href=\"http://care.email-newsletter.info/email/inside-ci-news-from-the-ci-secretariat-october-2016#humanitarian-response-in-haiti\">Read more</a> </strong></p>\n</td>\n</tr>\n<tr>\n<td style=\"background: #ffffff; padding: 20px 0px;\" colspan=\"2\" valign=\"top\"><img style=\"display: block;\" src=\"http://care.email-newsletter.info/files/images/btm_footer_line.gif\" width=\"518\" height=\"7\" /></td>\n</tr>\n<tr>\n<td style=\"padding: 0px; line-height: 16px; font-family: Arial, Helvetica, sans-serif; font-size: 13px;\" colspan=\"2\" valign=\"top\">\n<p style=\"color: #cc6633; line-height: 20px; font-family: Arial,Helvetica,sans-serif; font-size: 18px; font-weight: normal;\"><strong>Syria and the humanitarian response: persistence in the face of war</strong></p>\n<p><img style=\"width: 518px; height: 291px; max-width: 518px;\" src=\"http://care.email-newsletter.info/files/images/April%202016/Oct%202016/Syria%20recent%20distribution%20%202-scr_518x291.jpg\" alt=\"syria\" /></p>\n<p>CARE International has been stepping up its humanitarian advocacy on Syria following the devastation and killings in Aleppo. We share the frustration and horror of seeing colleagues in the UN and Red Cross increasingly being deliberately targeted, preventing assistance from reaching those in desperate need, while feeling powerless to stop it.<br /> <br /> Despite our limited influence, CARE has been at the forefront of discussions around what more we can do.</p>\n<p><strong><a href=\"http://care.email-newsletter.info/email/inside-ci-news-from-the-ci-secretariat-october-2016#syria-and-the-humanitarian-response\">Read more</a></strong></p>\n</td>\n</tr>\n<tr>\n<td style=\"background: #ffffff; padding: 20px 0px;\" colspan=\"2\" valign=\"top\"><img style=\"display: block;\" src=\"http://care.email-newsletter.info/files/images/btm_footer_line.gif\" width=\"518\" height=\"7\" /></td>\n</tr>\n<tr>\n<td style=\"padding: 0px; line-height: 16px; font-family: Arial, Helvetica, sans-serif; font-size: 13px;\" colspan=\"2\" valign=\"top\">\n<p style=\"color: #cc6633; line-height: 20px; font-family: Arial,Helvetica,sans-serif; font-size: 18px; font-weight: normal;\"><strong>Stories from South Sudan</strong></p>\n<p><img style=\"width: 518px; height: 346px; max-width: 518px;\" src=\"http://care.email-newsletter.info/files/images/April%202016/Oct%202016/south%20sudan_sept%202016_torit-31-scr_518x346.jpg\" alt=\"south sudan\" /></p>\n<p>The situation in&nbsp;South Sudan&nbsp;has rapidly deteriorated since the outbreak of violence across the country in July this year.<br /> <br /> CARE is one of the only big humanitarian agencies operating in the area and it is focusing on scaling up its emergency response with NFI (Non-Food-Items) distributions, potential SGBV/SRH support and food and livelihoods work.</p>\n<p><strong><a href=\"http://care.email-newsletter.info/email/inside-ci-news-from-the-ci-secretariat-october-2016#stories-from-south-sudan\">Read more</a></strong></p>\n</td>\n</tr>\n<tr>\n<td style=\"background: #ffffff; padding: 20px 0px;\" colspan=\"2\" valign=\"top\"><img style=\"display: block;\" src=\"http://care.email-newsletter.info/files/images/btm_footer_line.gif\" width=\"518\" height=\"7\" /></td>\n</tr>\n<tr>\n<td style=\"line-height: 16px; padding-right: 24px; font-family: Arial, Helvetica, sans-serif; font-size: 13px;\" valign=\"top\">\n<h3 style=\"color: #cc6633; line-height: 20px; font-family: Arial,Helvetica,sans-serif; font-size: 18px; font-weight: normal;\"><strong>CSO Governance and Mutual Accountability: How we are reforming now and reshaping for the future</strong></h3>\n<p><img style=\"width: 247px; height: 202px;\" src=\"http://care.email-newsletter.info/files/images/April%202016/Oct%202016/problem%20tree1_247x202.jpg\" alt=\"CI accountability\" /></p>\n<p><a href=\"http://www.ingoaccountabilitycharter.org/\">Accountable Now</a>, previously called the INGO Accountability Charter, is a global platform supporting civil society organisations to be transparent, responsive to stakeholders\' needs and focused on delivering impact.&nbsp;<br /> <br /> CARE has been a member for several years, and now with the new CI Accountability Framework under development, we have an exciting opportunity to better engage with, and contribute to, the platform.</p>\n<p><strong><a href=\"http://care.email-newsletter.info/email/inside-ci-news-from-the-ci-secretariat-october-2016#cso-governance-and-mutual-accountability\">Read more</a></strong></p>\n</td>\n<td style=\"line-height: 16px; font-family: Arial, Helvetica, sans-serif; font-size: 13px;\" valign=\"top\">\n<h3 style=\"color: #cc6633; line-height: 20px; font-family: Arial,Helvetica,sans-serif; font-size: 18px; font-weight: normal;\"><strong>Meet the Staff: Q&amp;A with Marten Mylius</strong><br /> &nbsp;</h3>\n<p><img style=\"width: 247px; height: 164px;\" src=\"http://care.email-newsletter.info/files/images/April%202016/Oct%202016/Marten_247x164.jpg\" alt=\"Marten Mylius\" /></p>\n<p>Based in Amman, Jordan, Marten Mylius&nbsp;has been&nbsp;Regional Emergency Coordinator (REC)&nbsp;for Middle East &amp; North Africa&nbsp;(MENA) at CARE International Secretariat&nbsp;for almost a year.&nbsp;<br /> <br /> He recently talked with us about his&nbsp;work and shared his insights on the current Middle East situation.</p>\n<p><a href=\"http://care.email-newsletter.info/email/inside-ci-news-from-the-ci-secretariat-october-2016#marten-mylius\"><strong>Read more</strong></a></p>\n</td>\n</tr>\n<tr>\n<td style=\"background: #ffffff; padding: 20px 0px;\" colspan=\"2\" valign=\"top\"><img style=\"display: block;\" src=\"http://care.email-newsletter.info/files/images/btm_footer_line.gif\" width=\"518\" height=\"7\" /></td>\n</tr>\n<tr>\n<td style=\"line-height: 16px; padding-right: 24px; font-family: Arial, Helvetica, sans-serif; font-size: 13px;\" valign=\"top\">\n<h3 style=\"color: #cc6633; line-height: 20px; font-family: Arial,Helvetica,sans-serif; font-size: 18px; font-weight: normal;\"><strong>UN Refugee and Migrant Summit: A show of goodwill that still falls short on&nbsp;commitments&nbsp;&nbsp;</strong></h3>\n<p><img style=\"width: 247px; height: 185px;\" src=\"http://care.email-newsletter.info/files/images/April%202016/Oct%202016/UNGA_247x185.jpg\" alt=\"UNGA\" /><br /> <span style=\"font-size: 12px;\"><em>Photo taken&nbsp;at&nbsp;CARE event on refugee women and girls, featuring three local partners and reps from CI-UK, CI-Canada and CI-Secretariat.</em></span></p>\n<p>World leaders gathered&nbsp;on September&nbsp;19 for the first ever UN Refugee and Migrant Summit to formally adopt a UN General Assembly (UNGA) resolution for addressing the global refugee crisis.<br /> <br /> This was supposed to be a moment when the world finally took a stand to protect and strengthen the rights of refugees, but instead fell incredibly short on concrete commitments to meet global needs. Despite this, CARE\'s participation remained very fruitful, marked by diverse representation, varied participation in several fora, and media pushes.</p>\n<p><strong><a href=\"http://care.email-newsletter.info/email/inside-ci-news-from-the-ci-secretariat-october-2016#un-refugee-and-migrant-summit\">Read more</a></strong></p>\n</td>\n<td style=\"line-height: 16px; font-family: Arial, Helvetica, sans-serif; font-size: 13px;\" valign=\"top\">\n<h3 style=\"color: #cc6633; line-height: 20px; font-family: Arial,Helvetica,sans-serif; font-size: 18px; font-weight: normal;\"><strong>Membership development:&nbsp;First business plans submitted</strong></h3>\n<p>Four candidate offices - CARE Egypt, CARE Indonesia, CARE Morocco and Chrysalis (a social enterprise spin-off from CARE Sri Lanka) -&nbsp;presented their business plans to the CARE&rsquo;s Membership Development Steering Committee (MDSC) in Casablanca at the end of September.</p>\n<p>Click <a href=\"http://care.email-newsletter.info/files/files/October%202016/CI%20Membership%20%20Update-%20MDSG%20%20Casablanca%20update%20Oct\'16.pdf\">here </a>to&nbsp;learn more on the outcome of this exciting and engaging meeting and outline of&nbsp;next steps.<br /> &nbsp;</p>\n<p><span style=\"font-size: 11px;\"><em>&nbsp;</em></span><img style=\"width: 247px; height: 182px;\" src=\"http://care.email-newsletter.info/files/images/April%202016/Oct%202016/team-collaboration_247x182.gif\" alt=\"membership development\" /></p>\n</td>\n</tr>\n<tr>\n<td style=\"background: #ffffff; padding: 20px 0px;\" colspan=\"2\" valign=\"top\"><img style=\"display: block;\" src=\"http://care.email-newsletter.info/files/images/btm_footer_line.gif\" width=\"518\" height=\"7\" /></td>\n</tr>\n<tr>\n<td style=\"line-height: 16px; padding-right: 24px; font-family: Arial, Helvetica, sans-serif; font-size: 13px;\" valign=\"top\" width=\"247\">\n<h3 style=\"color: #cc6633; line-height: 20px; font-family: Arial,Helvetica,sans-serif; font-size: 18px; font-weight: normal;\"><strong>...Looking back</strong></h3>\n<p><strong>7&nbsp;September: </strong>Under the patronage of the European Parliament, the multimedia exhibit celebrating the 70th anniversary of the CARE package was held by CARE International in Brussels. More than 50 invitees joined colleagues there to take a walk through humanitarian history at the Parliament building.&nbsp;Among those addressing the gathering were Martin Schulz, President of the European Parliament,&nbsp;Christos Stylianides, European Commissioner for Humanitarian Aid and Crisis Management, and Wolfgang Jamann, CEO and Secretary General of CARE International.</p>\n<p><strong>13 September: </strong>The blog post&nbsp;<a href=\"https://disrupt-and-innovate.org/is-futurism-a-fad/\">Is Futurism a Fad?</a> by Sarah Ralston, Head of Organisational Development &amp; Accountability at CI Secretariat, was published on the portal of the International Civil Society Centre.</p>\n<p><strong>21-23 September: </strong>CARE West Africa convened 32 CARE staff from 11 countries, 2 consultants, and 7 key partners in Abidjan, Ivory Coast, for a workshop around expanding CARE&rsquo;s work on Sexual and Reproductive Health and Rights (SRHR) in the region. As a result, a new SRHR strategy for the region will be developed with the aim of reaching 100 million women by 2020.</p>\n<p><strong>26-30 September: </strong>The first of the Regional Leadership Meetings kicked off in Abidjan,&nbsp;Ivory Coast. It focused on the WARMU area and it was an&nbsp;informative and&nbsp;constructive seminar, followed by a workshop.<br /> <br /> <strong>3-7 October: </strong>CARE\'s&nbsp;impact in Latin America and the Caribbean was the central theme&nbsp;of the LAC Regional Leadership Meeting in Lima, Peru.&nbsp;</p>\n<p><strong>4-6 October: </strong>The CARE Emergency Group (CEG) retreat took place in Morzine, France, where Philippe Guiton met the CEG team for the first time since joining CARE as Humanitarian and Operations Director in August 2016. The meeting focused on CEG management of type 3 and 4 emergencies.</p>\n</td>\n<td style=\"line-height: 16px; font-family: Arial, Helvetica, sans-serif; font-size: 13px;\" valign=\"top\" width=\"247\">\n<h3 style=\"color: #cc6633; line-height: 20px; font-family: Arial,Helvetica,sans-serif; font-size: 18px; font-weight: normal;\"><strong>Looking forward...</strong></h3>\n<p><strong>10-14 October:&nbsp;</strong>A CARE delegation of experts in food and nutrition security &amp; climate resilience (FNS &amp; CR) will participate in meetings in London, Brussels and Rome with delegates from DFID, DEVCO, EU, ESN, WFP and FAO. The objective is&nbsp;to share &nbsp;innovations, best practices, learning and impact coming from CARE&rsquo;s FNS&amp;CR portfolio.</p>\n<p><strong>11-13 October: </strong>The Emergency Response Working Group (ERWG) meets in&nbsp;Geneva to understand the relationship between the SLTs and the ERWG and review priority areas for 2017.</p>\n<p><strong>16 October: </strong>World Food Day.</p>\n<p><strong>17-21 October:&nbsp;</strong>The Asian Regional Leadership Meeting<strong> (</strong>ARMU) Regional Leadership Meeting will take place in Amman, Jordan.<br /> <br /> <strong>24-28 October: </strong>The Middle East and North Africa (MENA) Regional Leadership Meeting in Cairo, Egypt.</p>\n</td>\n</tr>\n<tr>\n<td style=\"line-height: 16px; padding-right: 24px; font-family: Arial, Helvetica, sans-serif; font-size: 13px;\" valign=\"top\" width=\"247\">&nbsp;</td>\n<td style=\"line-height: 16px; font-family: Arial, Helvetica, sans-serif; font-size: 13px;\" valign=\"top\" width=\"247\">&nbsp;</td>\n</tr>\n</tbody>\n</table>\n</td>\n</tr>\n</tbody>\n</table>\n<table border=\"0\" width=\"612\" cellspacing=\"0\" cellpadding=\"0\" align=\"center\">\n<tbody>\n<tr>\n<td style=\"background: #ffffff; padding: 20px 0px;\" colspan=\"2\"><img style=\"display: block;\" src=\"http://care.email-newsletter.info/files/images/btm_footer_line.gif\" width=\"612\" height=\"7\" /></td>\n</tr>\n<tr>\n<td style=\"background: #ffffff; padding: 10px 39px; color: #a84e07; font-family: Arial, Helvetica, sans-serif; font-size: 10px; font-weight: bold;\" colspan=\"2\">\n<p>Email Delivery - To ensure that future emails from CARE International are delivered to your inbox and are not treated as spam, please add <a style=\"color: #a84e07; text-decoration: underline;\" href=\"mailto:wjamann@careinternational.org\">ceo@careinternational.org</a> to your address book or list of approved senders.</p>\n<p>Unsubscribe - <a style=\"color: #a84e07; text-decoration: underline;\" href=\"http://care.email-newsletter.info/unsubscribe/%recipient.id%\">click here</a> or send an email to <a style=\"color: #a84e07; text-decoration: underline;\" href=\"mailto:cesarani@careinternational.org\">cesarani@careinternational.org</a><a href=\"mailto:cesarani@careinternational.org\">.</a></p>\n</td>\n</tr>\n</tbody>\n</table>\n<p>&nbsp;</p>', 0, 0, -2, NULL, '2017-09-14 12:16:13', '2017-09-18 16:15:10'),
(3, 1, 4, NULL, NULL, 'Changed Campaign to Tests', '<p>Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Donec quam felis, ultricies nec, pellentesque eu, pretium quis, sem. Nulla consequat massa quis enim. Donec pede justo, fringilla vel, aliquet nec, vulputate eget, arcu. In enim justo, rhoncus ut, imperdiet a, venenatis vitae, justo. Nullam dictum felis eu pede mollis pretium. Integer tincidunt. Cras dapibus.</p>\n<p>Vivamus elementum semper nisi. Aenean vulputate eleifend tellus. Aenean leo ligula, porttitor eu, consequat vitae, eleifend ac, enim. Aliquam lorem ante, dapibus in, viverra quis, feugiat a, tellus. Phasellus viverra nulla ut metus varius laoreet. Quisque rutrum. Aenean imperdiet. Etiam ultricies nisi vel augue. Curabitur ullamcorper ultricies nisi. Nam eget dui. Etiam rhoncus. Maecenas tempus, tellus eget condimentum rhoncus, sem quam semper libero, sit amet adipiscing sem neque sed ipsum.</p>\n<p>Nam quam nunc, blandit vel, luctus pulvinar, hendrerit id, lorem. Maecenas nec odio et ante tincidunt tempus. Donec vitae sapien ut libero venenatis faucibus. Nullam quis ante. Etiam sit amet orci eget eros faucibus tincidunt. Duis leo. Sed fringilla mauris sit amet nibh. Donec sodales sagittis magna. Sed consequat, leo eget bibendum sodales, augue velit cursus nunc, quis gravida magna mi a libero. Fusce vulputate eleifend sapien. Vestibulum purus quam, scelerisque ut, mollis sed, nonummy id, metus.</p>\n<pre class=\"language-javascript\"><code>        data() {\n            return {\n                fetchingData: true,\n                resource: {name: \'\', description: \'\', content: \'\'},\n                validationErrors: {name: \'\', description: \'\', content: \'\'},\n                editorReady: false\n            }\n        },</code></pre>\n<p>&nbsp;</p>', 0, 0, -2, NULL, '2017-09-14 16:37:21', '2017-09-18 16:12:12');

-- --------------------------------------------------------

--
-- Table structure for table `email_settings`
--

CREATE TABLE `email_settings` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `setting_value` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

-- --------------------------------------------------------

--
-- Table structure for table `mailing_lists`
--

CREATE TABLE `mailing_lists` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_deleted` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

--
-- Dumping data for table `mailing_lists`
--

INSERT INTO `mailing_lists` (`id`, `name`, `description`, `is_deleted`, `created_at`, `updated_at`) VALUES
(2, 'Stack Overflow', 'For information on how this works and the internals in MySQL and limits and such, see the other answer by Pekka.', 0, '2017-08-16 13:41:23', '2017-08-18 08:35:59'),
(4, 'Laravel List', 'In this example, we\'re passing the can middleware two arguments. The first is If the user is not authorized to perform the given action, a HTTP response with a 403 status code will be generated by the middleware.', 0, '2017-08-16 16:16:13', '2017-08-17 14:01:13'),
(5, 'How it works', 'Feedback messages may utilize the browser defaults (different for each browser, and unstylable via CSS) or our custom feedback styles with additional HTML and CSS.', 0, '2017-08-17 11:57:32', '2017-08-17 13:37:22'),
(6, 'Validation', 'Provide valuable, actionable feedback to your users with HTML5 form validation–available in all our supported browsers.', 0, '2017-08-17 13:36:04', '2017-08-17 15:43:47');

-- --------------------------------------------------------

--
-- Table structure for table `mailing_list_subscriber`
--

CREATE TABLE `mailing_list_subscriber` (
  `mailing_list_id` int(10) UNSIGNED NOT NULL,
  `subscriber_id` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

--
-- Dumping data for table `mailing_list_subscriber`
--

INSERT INTO `mailing_list_subscriber` (`mailing_list_id`, `subscriber_id`) VALUES
(5, 9),
(5, 3),
(5, 6),
(2, 6),
(2, 9),
(4, 3),
(4, 6),
(4, 9),
(4, 2),
(4, 1),
(2, 10),
(5, 10);

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(4, '2014_10_12_000000_create_users_table', 1),
(5, '2014_10_12_100000_create_password_resets_table', 1),
(7, '2017_08_16_092832_create_mailing_lists_table', 2),
(8, '2017_08_17_134411_create_subscribers_table', 3),
(9, '2017_08_17_145723_create_mailing_list_subscriber_table', 4),
(10, '2017_09_04_140849_create_campaigns_table', 5),
(11, '2017_09_04_163217_create_templates_table', 6),
(20, '2017_09_11_141311_create_email_settings_table', 7),
(23, '2017_09_12_122031_create_emails_table', 8);

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

-- --------------------------------------------------------

--
-- Table structure for table `subscribers`
--

CREATE TABLE `subscribers` (
  `id` int(10) UNSIGNED NOT NULL,
  `first_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '1',
  `is_deleted` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

--
-- Dumping data for table `subscribers`
--

INSERT INTO `subscribers` (`id`, `first_name`, `last_name`, `email`, `active`, `is_deleted`, `created_at`, `updated_at`) VALUES
(1, 'James', 'Brand Jamesy', 'james@acw.uk.com', 1, 0, '2017-08-17 16:32:35', '2017-08-30 17:03:20'),
(2, 'Cao', 'Ling', 'ling@acw.uk.com', 1, 0, '2017-08-17 16:33:12', '2017-08-30 17:03:20'),
(3, 'Ralph', 'Galan Son', 'ralph@acw.uk.com', 1, 0, '2017-08-18 14:41:29', '2017-08-30 15:48:07'),
(6, 'M83', 'Mates', 'mates@m83.com', 1, 1, '2017-08-24 11:56:58', '2017-09-18 12:37:34'),
(9, 'Test', 'Events', 'events@test.com', 1, 0, '2017-08-24 12:46:22', '2017-08-30 15:52:05'),
(10, 'Final', 'Test on Events Emission', 'hello@test.com', 1, 0, '2017-08-24 12:49:17', '2017-09-08 15:58:51');

-- --------------------------------------------------------

--
-- Table structure for table `templates`
--

CREATE TABLE `templates` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `content` mediumtext COLLATE utf8mb4_unicode_ci,
  `last_editor` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_deleted` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

--
-- Dumping data for table `templates`
--

INSERT INTO `templates` (`id`, `name`, `description`, `content`, `last_editor`, `is_deleted`, `created_at`, `updated_at`) VALUES
(1, 'Test', 'Description', '<p>The template plugin adds support for custom templates. It also adds a menu item Insert template under the Insert menu and a toolbar button.</p>\n<p>Type: String</p>\n<p>Example<br />tinymce.init({<br /> selector: \"textarea\", // change this value according to your HTML<br /> plugins: \"template\",<br /> menubar: \"insert\",<br /> toolbar: \"template\"<br />});</p>', 'James ILAKI', 0, '2017-09-04 17:11:35', '2017-09-11 11:56:00'),
(2, 'Test TinyMce', 'Hello TinyMce', '<p>Yes this is fine. Ok fine. That\'s all for now thanks. Over and out.</p>\n<p>&nbsp;</p>\n<p>Already this is things is working</p>', 'James ILAKI', 0, '2017-09-07 16:13:29', '2017-09-07 16:13:29'),
(4, 'Lorem Ipsum', 'Dummy Template', '<p><span style=\"font-family: arial,helvetica,sans-serif;\">Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Donec quam felis, ultricies nec, pellentesque eu, pretium quis, sem. Nulla consequat massa quis enim. Donec pede justo, fringilla vel, aliquet nec, vulputate eget, arcu. In enim justo, rhoncus ut, imperdiet a, venenatis vitae, justo. Nullam dictum felis eu pede mollis pretium. Integer tincidunt. Cras dapibus.</span></p>\n<p><img style=\"margin-right: 20px; margin-left: 20px; float: right;\" src=\"//localhost:3000/uploads/files/test/edit_post_screen.jpg\" alt=\"\" width=\"163\" height=\"92\" />Vivamus elementum semper nisi. Aenean vulputate eleifend tellus. Aenean leo ligula, porttitor eu, consequat vitae, eleifend ac, enim. Aliquam lorem ante, dapibus in, viverra quis, feugiat a, tellus. Phasellus viverra nulla ut metus varius laoreet. Quisque rutrum. Aenean imperdiet. Etiam ultricies nisi vel augue. Curabitur ullamcorper ultricies nisi. Nam eget dui. Etiam rhoncus. Maecenas tempus, tellus eget condimentum rhoncus, sem quam semper libero, sit amet adipiscing sem neque sed ipsum.</p>\n<p><img style=\"float: left; margin-right: 20px; margin-left: 20px;\" src=\"//localhost:3000/uploads/files/test/customised_footer.jpg\" width=\"300\" height=\"170\" />Nam quam nunc, blandit vel, luctus pulvinar, hendrerit id, lorem. Maecenas nec odio et ante tincidunt tempus. Donec vitae sapien ut libero venenatis faucibus. Nullam quis ante. Etiam sit amet orci eget eros faucibus tincidunt. Duis leo. Sed fringilla mauris sit amet nibh. Donec sodales sagittis magna. Sed consequat, leo eget bibendum sodales, augue velit cursus nunc, quis gravida magna mi a libero. Fusce vulputate eleifend sapien. Vestibulum purus quam, scelerisque ut, mollis sed, nonummy id, metus.</p>\n<p>Nullam accumsan lorem in dui. Cras ultricies mi eu turpis hendrerit fringilla. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae; In ac dui quis mi consectetuer lacinia. Nam pretium turpis et arcu. Duis arcu tortor, suscipit eget, imperdiet nec, imperdiet iaculis, ipsum. Sed aliquam ultrices mauris. Integer ante arcu, accumsan a, consectetuer eget, posuere ut, mauris. Praesent adipiscing. Phasellus ullamcorper ipsum rutrum nunc. Nunc nonummy metus.</p>\n<p>Different font, no?<img style=\"float: right;\" src=\"//localhost:3000/uploads/files/test-2/screenshot_1.jpg\" width=\"600\" height=\"290\" /></p>', 'James ILAKI', 1, '2017-09-08 08:52:40', '2017-09-08 11:40:29'),
(5, 'Can\'t Stop', 'Won\'t stop', '<pre class=\"language-javascript\"><code>        data() {\n            return {\n                fetchingData: true,\n                resource: {name: \'\', description: \'\', content: \'\'},\n                validationErrors: {name: \'\', description: \'\', content: \'\'},\n                editorReady: false\n            }\n        },</code></pre>\n<p>This is some Javascript code</p>', 'James ILAKI', 0, '2017-09-08 08:56:18', '2017-09-08 08:56:18'),
(6, 'Test Event Listening', 'Hello', '<p>Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Donec quam felis, ultricies nec, pellentesque eu, pretium quis, sem. Nulla consequat massa quis enim. Donec pede justo, fringilla vel, aliquet nec, vulputate eget, arcu. In enim justo, rhoncus ut, imperdiet a, venenatis vitae, justo. Nullam dictum felis eu pede mollis pretium. Integer tincidunt. Cras dapibus.</p>\n<p><code>Vivamus elementum semper nisi. Aenean vulputate eleifend tellus. Aenean leo ligula, porttitor eu, consequat vitae, eleifend ac, enim. Aliquam lorem ante, dapibus in, viverra quis, feugiat a, tellus. Phasellus viverra nulla ut metus varius laoreet. Quisque rutrum. Aenean imperdiet. Etiam ultricies nisi vel augue. Curabitur ullamcorper ultricies nisi. Nam eget dui. Etiam rhoncus. Maecenas tempus, tellus eget condimentum rhoncus, sem quam semper libero, sit amet adipiscing sem neque sed ipsum.</code></p>\n<p>Nam quam nunc, blandit vel, luctus pulvinar, hendrerit id, lorem. Maecenas nec odio et ante tincidunt tempus. Donec vitae sapien ut libero venenatis faucibus. Nullam quis ante. Etiam sit amet orci eget eros faucibus tincidunt. Duis leo. Sed fringilla mauris sit amet nibh. Donec sodales sagittis magna. Sed consequat, leo eget bibendum sodales, augue velit cursus nunc, quis gravida magna mi a libero. Fusce vulputate eleifend sapien. Vestibulum purus quam, scelerisque ut, mollis sed, nonummy id, metus.</p>\n<p>Nullam accumsan lorem in dui. Cras ultricies mi eu turpis hendrerit fringilla. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae; In ac dui quis mi consectetuer lacinia. Nam pretium turpis et arcu. Duis arcu tortor, suscipit eget, imperdiet nec, imperdiet iaculis, ipsum. Sed aliquam ultrices mauris. Integer ante arcu, accumsan a, consectetuer eget, posuere ut, mauris. Praesent adipiscing. Phasellus ullamcorper ipsum rutrum nunc. Nunc nonummy metus.</p>', 'James ILAKI', 0, '2017-09-08 09:11:16', '2017-09-08 09:11:16'),
(7, 'Last Test', 'Done', '<p>Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Donec quam felis, ultricies nec, pellentesque eu, pretium quis, sem. Nulla consequat massa quis enim. Donec pede justo, fringilla vel, aliquet nec, vulputate eget, arcu. In enim justo, rhoncus ut, imperdiet a, venenatis vitae, justo. Nullam dictum felis eu pede mollis pretium. Integer tincidunt. Cras dapibus.</p>\n<p>Vivamus elementum semper nisi. Aenean vulputate eleifend tellus. Aenean leo ligula, porttitor eu, consequat vitae, eleifend ac, enim. Aliquam lorem ante, dapibus in, viverra quis, feugiat a, tellus. Phasellus viverra nulla ut metus varius laoreet. Quisque rutrum. Aenean imperdiet. Etiam ultricies nisi vel augue. Curabitur ullamcorper ultricies nisi. Nam eget dui. Etiam rhoncus. Maecenas tempus, tellus eget condimentum rhoncus, sem quam semper libero, sit amet adipiscing sem neque sed ipsum.</p>\n<p>Nam quam nunc, blandit vel, luctus pulvinar, hendrerit id, lorem. Maecenas nec odio et ante tincidunt tempus. Donec vitae sapien ut libero venenatis faucibus. Nullam quis ante. Etiam sit amet orci eget eros faucibus tincidunt. Duis leo. Sed fringilla mauris sit amet nibh. Donec sodales sagittis magna. Sed consequat, leo eget bibendum sodales, augue velit cursus nunc, quis gravida magna mi a libero. Fusce vulputate eleifend sapien. Vestibulum purus quam, scelerisque ut, mollis sed, nonummy id, metus.</p>\n<p>Nullam accumsan lorem in dui. Cras ultricies mi eu turpis hendrerit fringilla. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae; In ac dui quis mi consectetuer lacinia. Nam pretium turpis et arcu. Duis arcu tortor, suscipit eget, imperdiet nec, imperdiet iaculis, ipsum. Sed aliquam ultrices mauris. Integer ante arcu, accumsan a, consectetuer eget, posuere ut, mauris. Praesent adipiscing. Phasellus ullamcorper ipsum rutrum nunc. Nunc nonummy metus..</p>', 'James ILAKI', 0, '2017-09-08 09:27:44', '2017-09-08 11:45:48'),
(8, 'CARE International', 'Test iframe', '<table border=\"0\" width=\"612\" cellspacing=\"0\" cellpadding=\"0\" align=\"center\">\n<tbody>\n<tr>\n<td colspan=\"2\" valign=\"top\"><img style=\"display: block;\" src=\"http://care.email-newsletter.info/files/images/top_banner.gif\" alt=\"CARE\" usemap=\"#map1\" width=\"612\" height=\"145\" border=\"0\" /></td>\n</tr>\n<tr>\n<td style=\"background: #e4701e; height: 33px; color: #ffffff; line-height: 33px; padding-right: 33px; font-family: Arial, Helvetica, sans-serif; font-size: 16px; font-weight: bold;\" colspan=\"2\" align=\"right\" valign=\"top\">Inside CI: News from the CI Secretariat <span style=\"color: #fdb928;\">| November 2016</span></td>\n</tr>\n<tr>\n<td colspan=\"2\" valign=\"top\"><img style=\"display: block;\" src=\"http://care.email-newsletter.info/files/images/below_header_two.gif\" alt=\"CARE in Emergencies\" width=\"612\" height=\"26\" /></td>\n</tr>\n<tr>\n<td style=\"background: #ffffff;\" valign=\"top\">\n<table border=\"0\" width=\"518\" cellspacing=\"0\" cellpadding=\"5\" align=\"center\">\n<tbody>\n<tr>\n<td style=\"line-height: 16px; font-family: Arial, Helvetica, sans-serif; font-size: 13px;\" colspan=\"2\" valign=\"top\">\n<h3 style=\"color: #cc6633; line-height: 26px; font-family: Arial, Helvetica, sans-serif; font-size: 22px;\">Dear Colleges,</h3>\n<p style=\"line-height: 12.0pt;\"><span style=\"font-size: 10.0pt;\">Recently, a number of internal planning and strategic meetings went into high gear and the level of excitement and expectation around the network is almost palpable: The newly welcomed members of the CARE International Supervisory Board met for the first time in Geneva and showed outstanding commitment to helping us progress the CARE 2020 vision; two Town Hall meetings were widely attended by colleagues via webex, giving us the opportunity to discuss the organisation&rsquo;s challenges and milestones towards this vision. </span></p>\n<p style=\"line-height: 12.0pt;\"><span style=\"font-size: 10.0pt;\">While all of this is motivating in and of itself, there is always a special, renewed energy when visiting the field to discuss change and improvements in the way we meet the future. The West Africa Regional Leadership Meeting in Abidjan, Cote d&rsquo;Ivoire was a welcome opportunity to witness the dynamics around impact, growth, and strategy. Collectively, we recognise that working out objectives for a new accountability framework and ensuring robust membership development are activities directly linked to delivering on our programme strategy on the ground. In Africa, as in many parts of the world, CARE&rsquo;s signature initiatives and approaches such as the Village Savings and Loans Associations have the potential to amplify gains in resilience, inclusive governance and women&rsquo;s voice, as evidenced by the visit to communities in the cocoa-producing regions of Cote d&rsquo;Ivoire.&nbsp;</span></p>\n<p style=\"line-height: 12.0pt;\"><span style=\"font-size: 10.0pt;\">And as we met in our various planning sessions, humanitarian crises raged on &ndash; a powerful reminder of our work at hand. We were encouraged by the sterling work of our CARE country teams and communication colleagues to try and alleviate the suffering wrought by Hurricane Matthew in Haiti and bring it to the world&rsquo;s attention.&nbsp; We did so while trying not to forget the many other places in need and on the brink of starvation, such as Yemen</span><span style=\"font-size: 10.0pt;\">&nbsp;as <a href=\"http://www.youtube.com/watch?v=npk7tfKyXok\">this video</a> shows. </span></p>\n<p style=\"line-height: 12.0pt;\"><span style=\"font-size: 10.0pt;\">In this newsletter, we also highlight CARE&rsquo;s agreement with the International Food Policy Research Institute, playing our part to help end world hunger by 2030, if not earlier. October 16 every year is World Food Day so it was a timely signing of the partnership agreement. Meeting the food challenge means tackling the unjust fact that, in most instances, hunger is caused by man-made actions, conflict, violence, bad governance and climate change. And as with so many other challenges, we can therefore only reach our goal, if we continue to address hunger&rsquo;s underlying, political causes. </span></p>\n<p style=\"line-height: 12.0pt;\"><span style=\"font-size: 10.0pt;\">With warm regards,&nbsp;</span></p>\n<p><strong>Wolfgang Jamann<br /> Secretary General/CEO CARE International</strong></p>\n</td>\n</tr>\n<tr>\n<td style=\"background: #ffffff; padding: 20px 0px;\" colspan=\"2\" valign=\"top\"><img style=\"display: block;\" src=\"http://care.email-newsletter.info/files/images/btm_footer_line.gif\" width=\"518\" height=\"7\" /></td>\n</tr>\n<tr>\n<td style=\"padding: 0px; line-height: 16px; font-family: Arial, Helvetica, sans-serif; font-size: 13px;\" colspan=\"2\" valign=\"top\">\n<h3 style=\"color: #cc6633; line-height: 20px; font-family: Arial,Helvetica,sans-serif; font-size: 18px; font-weight: normal;\"><strong>First Supervisory Board meeting paves the way to CARE\'s transformational journey</strong></h3>\n<p><img style=\"width: 518px; height: 261px;\" src=\"http://care.email-newsletter.info/files/images/April%202016/Oct%202016/Board%20-%20pictures_518x261.png\" alt=\"Board members\" /><br /> <span style=\"font-size: 12px;\"><em>From top left, clockwise: Louise Frechette, Chairperson; Paul Jansen; Namrata Kaul; Reki Moussa Hassan;&nbsp;Susan Liautaud; Arielle de Rothschild.</em></span></p>\n<p>The inaugural CI Supervisory Board meeting in Geneva at the end of September marked a pivotal moment with discussions covering a range of topics, and with decisions on rules of procedure and the Board&rsquo;s role, as well as inputting and strategically positioning around key shared priorities.<br /> <br /> <a href=\"http://care.email-newsletter.info/email/inside-ci-news-from-the-ci-secretariat-october-2016#first-supervisory-board-meeting\"><span style=\"color: #ff8c00;\"><strong>Read more</strong></span></a></p>\n</td>\n</tr>\n<tr>\n<td style=\"background: #ffffff; padding: 20px 0px;\" colspan=\"2\" valign=\"top\"><img style=\"display: block;\" src=\"http://care.email-newsletter.info/files/images/btm_footer_line.gif\" width=\"518\" height=\"7\" /></td>\n</tr>\n<tr>\n<td style=\"padding: 0px; line-height: 16px; font-family: Arial, Helvetica, sans-serif; font-size: 13px;\" colspan=\"2\" valign=\"top\">\n<p style=\"color: #cc6633; line-height: 20px; font-family: Arial,Helvetica,sans-serif; font-size: 18px; font-weight: normal;\"><strong>New partnership sees CARE and IFPRI join forces to end world hunger</strong></p>\n<p><img style=\"width: 518px; height: 346px; max-width: 518px;\" src=\"http://care.email-newsletter.info/files/images/April%202016/Oct%202016/IFPRI%20signature_518x346.jpg\" alt=\"IFPRI\" /><br /> <span style=\"font-size: 12px;\"><em><span style=\"font-family: arial,helvetica,sans-serif;\">Wolfgang Jamann and <span lang=\"EN-US\" style=\"line-height: 107%; mso-fareast-font-family: Calibri; mso-fareast-theme-font: minor-latin;\"><span style=\"color: #000000;\">Shenggen Fan, Director General of IFPRI, shake hands after signing the Partnership Agreement.</span></span></span></em></span></p>\n<p>CARE International and the International Food Policy Research Institute (IFPRI) are strengthening collaboration with the aim of achieving food security, improving nutrition, and ending poverty. This objective aligns fully with the missions of both institutions and to ensure its achievement, the proposed collaboration will maximise CARE&rsquo;s extensive network and on-the-ground experience as well as IFPRI&rsquo;s high-calibre research capabilities.</p>\n<p>On October 6, Wolfgang Jamann,&nbsp;who also serves on the IFPRI Leadership Council, attended the Memorandum of Understanding (MoU)-signing event on behalf of CARE, a timely way to mark the upcoming World Food Day on October 16.</p>\n<p><strong><a href=\"http://care.email-newsletter.info/email/inside-ci-news-from-the-ci-secretariat-october-2016#care-and-ifpri\">Read more</a> </strong></p>\n</td>\n</tr>\n<tr>\n<td style=\"background: #ffffff; padding: 20px 0px;\" colspan=\"2\" valign=\"top\"><img style=\"display: block;\" src=\"http://care.email-newsletter.info/files/images/btm_footer_line.gif\" width=\"518\" height=\"7\" /></td>\n</tr>\n<tr>\n<td style=\"padding: 0px; line-height: 16px; font-family: Arial, Helvetica, sans-serif; font-size: 13px;\" colspan=\"2\" valign=\"top\">\n<p style=\"color: #cc6633; line-height: 20px; font-family: Arial,Helvetica,sans-serif; font-size: 18px; font-weight: normal;\"><strong>CARE scaling up humanitarian response in Haiti </strong></p>\n<p><img style=\"width: 518px; height: 345px; max-width: 518px;\" src=\"http://care.email-newsletter.info/files/images/April%202016/Oct%202016/haiti%20food_518x345.jpg\" alt=\"haiti\" /></p>\n<p>Hurricane Matthew crept up on Haiti and picked up such strength that it dumped massive rainfall,&nbsp;and left thousands in need of emergency assistance.</p>\n<p>CARE was well prepared with pre-positioned relief supplies. Immediately, CARE teams started distributing food and clean water to over 3,700 people in evacuation shelters in Port-au-Prince, the southeast and in Grande Anse Departments, where the brunt of the storm hit, with plans to to provide tarps and hygiene kits to some 50,000 people.</p>\n<p><strong><a href=\"http://care.email-newsletter.info/email/inside-ci-news-from-the-ci-secretariat-october-2016#humanitarian-response-in-haiti\">Read more</a> </strong></p>\n</td>\n</tr>\n<tr>\n<td style=\"background: #ffffff; padding: 20px 0px;\" colspan=\"2\" valign=\"top\"><img style=\"display: block;\" src=\"http://care.email-newsletter.info/files/images/btm_footer_line.gif\" width=\"518\" height=\"7\" /></td>\n</tr>\n<tr>\n<td style=\"padding: 0px; line-height: 16px; font-family: Arial, Helvetica, sans-serif; font-size: 13px;\" colspan=\"2\" valign=\"top\">\n<p style=\"color: #cc6633; line-height: 20px; font-family: Arial,Helvetica,sans-serif; font-size: 18px; font-weight: normal;\"><strong>Syria and the humanitarian response: persistence in the face of war</strong></p>\n<p><img style=\"width: 518px; height: 291px; max-width: 518px;\" src=\"http://care.email-newsletter.info/files/images/April%202016/Oct%202016/Syria%20recent%20distribution%20%202-scr_518x291.jpg\" alt=\"syria\" /></p>\n<p>CARE International has been stepping up its humanitarian advocacy on Syria following the devastation and killings in Aleppo. We share the frustration and horror of seeing colleagues in the UN and Red Cross increasingly being deliberately targeted, preventing assistance from reaching those in desperate need, while feeling powerless to stop it.<br /> <br /> Despite our limited influence, CARE has been at the forefront of discussions around what more we can do.</p>\n<p><strong><a href=\"http://care.email-newsletter.info/email/inside-ci-news-from-the-ci-secretariat-october-2016#syria-and-the-humanitarian-response\">Read more</a></strong></p>\n</td>\n</tr>\n<tr>\n<td style=\"background: #ffffff; padding: 20px 0px;\" colspan=\"2\" valign=\"top\"><img style=\"display: block;\" src=\"http://care.email-newsletter.info/files/images/btm_footer_line.gif\" width=\"518\" height=\"7\" /></td>\n</tr>\n<tr>\n<td style=\"padding: 0px; line-height: 16px; font-family: Arial, Helvetica, sans-serif; font-size: 13px;\" colspan=\"2\" valign=\"top\">\n<p style=\"color: #cc6633; line-height: 20px; font-family: Arial,Helvetica,sans-serif; font-size: 18px; font-weight: normal;\"><strong>Stories from South Sudan</strong></p>\n<p><img style=\"width: 518px; height: 346px; max-width: 518px;\" src=\"http://care.email-newsletter.info/files/images/April%202016/Oct%202016/south%20sudan_sept%202016_torit-31-scr_518x346.jpg\" alt=\"south sudan\" /></p>\n<p>The situation in&nbsp;South Sudan&nbsp;has rapidly deteriorated since the outbreak of violence across the country in July this year.<br /> <br /> CARE is one of the only big humanitarian agencies operating in the area and it is focusing on scaling up its emergency response with NFI (Non-Food-Items) distributions, potential SGBV/SRH support and food and livelihoods work.</p>\n<p><strong><a href=\"http://care.email-newsletter.info/email/inside-ci-news-from-the-ci-secretariat-october-2016#stories-from-south-sudan\">Read more</a></strong></p>\n</td>\n</tr>\n<tr>\n<td style=\"background: #ffffff; padding: 20px 0px;\" colspan=\"2\" valign=\"top\"><img style=\"display: block;\" src=\"http://care.email-newsletter.info/files/images/btm_footer_line.gif\" width=\"518\" height=\"7\" /></td>\n</tr>\n<tr>\n<td style=\"line-height: 16px; padding-right: 24px; font-family: Arial, Helvetica, sans-serif; font-size: 13px;\" valign=\"top\">\n<h3 style=\"color: #cc6633; line-height: 20px; font-family: Arial,Helvetica,sans-serif; font-size: 18px; font-weight: normal;\"><strong>CSO Governance and Mutual Accountability: How we are reforming now and reshaping for the future</strong></h3>\n<p><img style=\"width: 247px; height: 202px;\" src=\"http://care.email-newsletter.info/files/images/April%202016/Oct%202016/problem%20tree1_247x202.jpg\" alt=\"CI accountability\" /></p>\n<p><a href=\"http://www.ingoaccountabilitycharter.org/\">Accountable Now</a>, previously called the INGO Accountability Charter, is a global platform supporting civil society organisations to be transparent, responsive to stakeholders\' needs and focused on delivering impact.&nbsp;<br /> <br /> CARE has been a member for several years, and now with the new CI Accountability Framework under development, we have an exciting opportunity to better engage with, and contribute to, the platform.</p>\n<p><strong><a href=\"http://care.email-newsletter.info/email/inside-ci-news-from-the-ci-secretariat-october-2016#cso-governance-and-mutual-accountability\">Read more</a></strong></p>\n</td>\n<td style=\"line-height: 16px; font-family: Arial, Helvetica, sans-serif; font-size: 13px;\" valign=\"top\">\n<h3 style=\"color: #cc6633; line-height: 20px; font-family: Arial,Helvetica,sans-serif; font-size: 18px; font-weight: normal;\"><strong>Meet the Staff: Q&amp;A with Marten Mylius</strong><br /> &nbsp;</h3>\n<p><img style=\"width: 247px; height: 164px;\" src=\"http://care.email-newsletter.info/files/images/April%202016/Oct%202016/Marten_247x164.jpg\" alt=\"Marten Mylius\" /></p>\n<p>Based in Amman, Jordan, Marten Mylius&nbsp;has been&nbsp;Regional Emergency Coordinator (REC)&nbsp;for Middle East &amp; North Africa&nbsp;(MENA) at CARE International Secretariat&nbsp;for almost a year.&nbsp;<br /> <br /> He recently talked with us about his&nbsp;work and shared his insights on the current Middle East situation.</p>\n<p><a href=\"http://care.email-newsletter.info/email/inside-ci-news-from-the-ci-secretariat-october-2016#marten-mylius\"><strong>Read more</strong></a></p>\n</td>\n</tr>\n<tr>\n<td style=\"background: #ffffff; padding: 20px 0px;\" colspan=\"2\" valign=\"top\"><img style=\"display: block;\" src=\"http://care.email-newsletter.info/files/images/btm_footer_line.gif\" width=\"518\" height=\"7\" /></td>\n</tr>\n<tr>\n<td style=\"line-height: 16px; padding-right: 24px; font-family: Arial, Helvetica, sans-serif; font-size: 13px;\" valign=\"top\">\n<h3 style=\"color: #cc6633; line-height: 20px; font-family: Arial,Helvetica,sans-serif; font-size: 18px; font-weight: normal;\"><strong>UN Refugee and Migrant Summit: A show of goodwill that still falls short on&nbsp;commitments&nbsp;&nbsp;</strong></h3>\n<p><img style=\"width: 247px; height: 185px;\" src=\"http://care.email-newsletter.info/files/images/April%202016/Oct%202016/UNGA_247x185.jpg\" alt=\"UNGA\" /><br /> <span style=\"font-size: 12px;\"><em>Photo taken&nbsp;at&nbsp;CARE event on refugee women and girls, featuring three local partners and reps from CI-UK, CI-Canada and CI-Secretariat.</em></span></p>\n<p>World leaders gathered&nbsp;on September&nbsp;19 for the first ever UN Refugee and Migrant Summit to formally adopt a UN General Assembly (UNGA) resolution for addressing the global refugee crisis.<br /> <br /> This was supposed to be a moment when the world finally took a stand to protect and strengthen the rights of refugees, but instead fell incredibly short on concrete commitments to meet global needs. Despite this, CARE\'s participation remained very fruitful, marked by diverse representation, varied participation in several fora, and media pushes.</p>\n<p><strong><a href=\"http://care.email-newsletter.info/email/inside-ci-news-from-the-ci-secretariat-october-2016#un-refugee-and-migrant-summit\">Read more</a></strong></p>\n</td>\n<td style=\"line-height: 16px; font-family: Arial, Helvetica, sans-serif; font-size: 13px;\" valign=\"top\">\n<h3 style=\"color: #cc6633; line-height: 20px; font-family: Arial,Helvetica,sans-serif; font-size: 18px; font-weight: normal;\"><strong>Membership development:&nbsp;First business plans submitted</strong></h3>\n<p>Four candidate offices - CARE Egypt, CARE Indonesia, CARE Morocco and Chrysalis (a social enterprise spin-off from CARE Sri Lanka) -&nbsp;presented their business plans to the CARE&rsquo;s Membership Development Steering Committee (MDSC) in Casablanca at the end of September.</p>\n<p>Click <a href=\"http://care.email-newsletter.info/files/files/October%202016/CI%20Membership%20%20Update-%20MDSG%20%20Casablanca%20update%20Oct\'16.pdf\">here </a>to&nbsp;learn more on the outcome of this exciting and engaging meeting and outline of&nbsp;next steps.<br /> &nbsp;</p>\n<p><span style=\"font-size: 11px;\"><em>&nbsp;</em></span><img style=\"width: 247px; height: 182px;\" src=\"http://care.email-newsletter.info/files/images/April%202016/Oct%202016/team-collaboration_247x182.gif\" alt=\"membership development\" /></p>\n</td>\n</tr>\n<tr>\n<td style=\"background: #ffffff; padding: 20px 0px;\" colspan=\"2\" valign=\"top\"><img style=\"display: block;\" src=\"http://care.email-newsletter.info/files/images/btm_footer_line.gif\" width=\"518\" height=\"7\" /></td>\n</tr>\n<tr>\n<td style=\"line-height: 16px; padding-right: 24px; font-family: Arial, Helvetica, sans-serif; font-size: 13px;\" valign=\"top\" width=\"247\">\n<h3 style=\"color: #cc6633; line-height: 20px; font-family: Arial,Helvetica,sans-serif; font-size: 18px; font-weight: normal;\"><strong>...Looking back</strong></h3>\n<p><strong>7&nbsp;September: </strong>Under the patronage of the European Parliament, the multimedia exhibit celebrating the 70th anniversary of the CARE package was held by CARE International in Brussels. More than 50 invitees joined colleagues there to take a walk through humanitarian history at the Parliament building.&nbsp;Among those addressing the gathering were Martin Schulz, President of the European Parliament,&nbsp;Christos Stylianides, European Commissioner for Humanitarian Aid and Crisis Management, and Wolfgang Jamann, CEO and Secretary General of CARE International.</p>\n<p><strong>13 September: </strong>The blog post&nbsp;<a href=\"https://disrupt-and-innovate.org/is-futurism-a-fad/\">Is Futurism a Fad?</a> by Sarah Ralston, Head of Organisational Development &amp; Accountability at CI Secretariat, was published on the portal of the International Civil Society Centre.</p>\n<p><strong>21-23 September: </strong>CARE West Africa convened 32 CARE staff from 11 countries, 2 consultants, and 7 key partners in Abidjan, Ivory Coast, for a workshop around expanding CARE&rsquo;s work on Sexual and Reproductive Health and Rights (SRHR) in the region. As a result, a new SRHR strategy for the region will be developed with the aim of reaching 100 million women by 2020.</p>\n<p><strong>26-30 September: </strong>The first of the Regional Leadership Meetings kicked off in Abidjan,&nbsp;Ivory Coast. It focused on the WARMU area and it was an&nbsp;informative and&nbsp;constructive seminar, followed by a workshop.<br /> <br /> <strong>3-7 October: </strong>CARE\'s&nbsp;impact in Latin America and the Caribbean was the central theme&nbsp;of the LAC Regional Leadership Meeting in Lima, Peru.&nbsp;</p>\n<p><strong>4-6 October: </strong>The CARE Emergency Group (CEG) retreat took place in Morzine, France, where Philippe Guiton met the CEG team for the first time since joining CARE as Humanitarian and Operations Director in August 2016. The meeting focused on CEG management of type 3 and 4 emergencies.</p>\n</td>\n<td style=\"line-height: 16px; font-family: Arial, Helvetica, sans-serif; font-size: 13px;\" valign=\"top\" width=\"247\">\n<h3 style=\"color: #cc6633; line-height: 20px; font-family: Arial,Helvetica,sans-serif; font-size: 18px; font-weight: normal;\"><strong>Looking forward...</strong></h3>\n<p><strong>10-14 October:&nbsp;</strong>A CARE delegation of experts in food and nutrition security &amp; climate resilience (FNS &amp; CR) will participate in meetings in London, Brussels and Rome with delegates from DFID, DEVCO, EU, ESN, WFP and FAO. The objective is&nbsp;to share &nbsp;innovations, best practices, learning and impact coming from CARE&rsquo;s FNS&amp;CR portfolio.</p>\n<p><strong>11-13 October: </strong>The Emergency Response Working Group (ERWG) meets in&nbsp;Geneva to understand the relationship between the SLTs and the ERWG and review priority areas for 2017.</p>\n<p><strong>16 October: </strong>World Food Day.</p>\n<p><strong>17-21 October:&nbsp;</strong>The Asian Regional Leadership Meeting<strong> (</strong>ARMU) Regional Leadership Meeting will take place in Amman, Jordan.<br /> <br /> <strong>24-28 October: </strong>The Middle East and North Africa (MENA) Regional Leadership Meeting in Cairo, Egypt.</p>\n</td>\n</tr>\n<tr>\n<td style=\"line-height: 16px; padding-right: 24px; font-family: Arial, Helvetica, sans-serif; font-size: 13px;\" valign=\"top\" width=\"247\">&nbsp;</td>\n<td style=\"line-height: 16px; font-family: Arial, Helvetica, sans-serif; font-size: 13px;\" valign=\"top\" width=\"247\">&nbsp;</td>\n</tr>\n</tbody>\n</table>\n</td>\n</tr>\n</tbody>\n</table>\n<table border=\"0\" width=\"612\" cellspacing=\"0\" cellpadding=\"0\" align=\"center\">\n<tbody>\n<tr>\n<td style=\"background: #ffffff; padding: 20px 0px;\" colspan=\"2\"><img style=\"display: block;\" src=\"http://care.email-newsletter.info/files/images/btm_footer_line.gif\" width=\"612\" height=\"7\" /></td>\n</tr>\n<tr>\n<td style=\"background: #ffffff; padding: 10px 39px; color: #a84e07; font-family: Arial, Helvetica, sans-serif; font-size: 10px; font-weight: bold;\" colspan=\"2\">\n<p>Email Delivery - To ensure that future emails from CARE International are delivered to your inbox and are not treated as spam, please add <a style=\"color: #a84e07; text-decoration: underline;\" href=\"mailto:wjamann@careinternational.org\">ceo@careinternational.org</a> to your address book or list of approved senders.</p>\n<p>Unsubscribe - <a style=\"color: #a84e07; text-decoration: underline;\" href=\"http://care.email-newsletter.info/unsubscribe/%recipient.id%\">click here</a> or send an email to <a style=\"color: #a84e07; text-decoration: underline;\" href=\"mailto:cesarani@careinternational.org\">cesarani@careinternational.org</a><a href=\"mailto:cesarani@careinternational.org\">.</a></p>\n</td>\n</tr>\n</tbody>\n</table>\n<p>&nbsp;</p>', 'James ILAKI', 0, '2017-09-08 10:40:42', '2017-09-08 10:40:42');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(10) UNSIGNED NOT NULL,
  `first_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `username` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '1',
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `meta` mediumtext COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `first_name`, `last_name`, `username`, `email`, `password`, `active`, `remember_token`, `meta`, `created_at`, `updated_at`) VALUES
(1, 'James', 'Ilaki', 'Jamesy', 'james@acw.uk.com', '$2y$10$jSnM1zXorak0nNQPx2tBpOb5jdXUrzc/dFiMfkm3wDIrDnMvqKbsu', 1, '083yVedF8ZwGPGni44IRf0zkeV3sZNlH37GrWajBRouqAaxjSEwII87Oqkku', '{\"role\":\"Super Administrator\"}', '2017-08-16 09:12:25', '2017-08-16 09:25:21'),
(2, 'Ling', 'Cao', 'Ling', 'ling@acw.uk.com', '$2y$10$M3wBJh5i6QPVfrpIqocEwecudnPj8Kx0RTRtd5KmEsim7YNH.Pu8K', 1, 'fAm8LQJwLaJPOJDQe4ml6JXan9IJbKh6dcqZcGw2wZqGuFxJvPsjGL2K5Cfy', '{\"role\":\"User\",\"create_users\":true,\"read_users\":true,\"read_mailing_lists\":true,\"update_mailing_lists\":true,\"update_users\":true,\"create_mailing_lists\":true}', '2017-08-16 14:16:38', '2017-08-16 16:14:23');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `campaigns`
--
ALTER TABLE `campaigns`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `campaigns_name_unique` (`name`);

--
-- Indexes for table `emails`
--
ALTER TABLE `emails`
  ADD PRIMARY KEY (`id`),
  ADD KEY `emails_user_id_foreign` (`user_id`),
  ADD KEY `emails_campaign_id_foreign` (`campaign_id`);

--
-- Indexes for table `email_settings`
--
ALTER TABLE `email_settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `mailing_lists`
--
ALTER TABLE `mailing_lists`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `mailing_lists_name_unique` (`name`);

--
-- Indexes for table `mailing_list_subscriber`
--
ALTER TABLE `mailing_list_subscriber`
  ADD KEY `mailing_list_subscriber_mailing_list_id_foreign` (`mailing_list_id`),
  ADD KEY `mailing_list_subscriber_subscriber_id_foreign` (`subscriber_id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD KEY `password_resets_email_index` (`email`);

--
-- Indexes for table `subscribers`
--
ALTER TABLE `subscribers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `subscribers_email_unique` (`email`);

--
-- Indexes for table `templates`
--
ALTER TABLE `templates`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `templates_name_unique` (`name`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_username_unique` (`username`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `campaigns`
--
ALTER TABLE `campaigns`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT for table `emails`
--
ALTER TABLE `emails`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT for table `email_settings`
--
ALTER TABLE `email_settings`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `mailing_lists`
--
ALTER TABLE `mailing_lists`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;
--
-- AUTO_INCREMENT for table `subscribers`
--
ALTER TABLE `subscribers`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;
--
-- AUTO_INCREMENT for table `templates`
--
ALTER TABLE `templates`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;
--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- Constraints for dumped tables
--

--
-- Constraints for table `emails`
--
ALTER TABLE `emails`
  ADD CONSTRAINT `emails_campaign_id_foreign` FOREIGN KEY (`campaign_id`) REFERENCES `campaigns` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `emails_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `mailing_list_subscriber`
--
ALTER TABLE `mailing_list_subscriber`
  ADD CONSTRAINT `mailing_list_subscriber_mailing_list_id_foreign` FOREIGN KEY (`mailing_list_id`) REFERENCES `mailing_lists` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `mailing_list_subscriber_subscriber_id_foreign` FOREIGN KEY (`subscriber_id`) REFERENCES `subscribers` (`id`) ON DELETE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
