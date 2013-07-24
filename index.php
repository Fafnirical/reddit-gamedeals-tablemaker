<?php

//display area for input, w/ text area
//get URL on "SUBMIT" button

//function checks URL schema (NOTE: disable for initial testing purposes)
 /* if domain is Playfire.com

  * function gets data from: div.columns-fauxcolumns div.columns-inner div.column-center-outer div.column-center-inner div#main.main.section div.widget.Blog div.blog-posts.hfeed div.date-outer div.date-posts div.post-outer div.post-hentry div.post-body.entry-content ul
  * (NOTE: disable for initial testing purposes)
  *
  * function then does a foreach li in ul[]; this is each game

  * function parses for: a->href; a->text
  * function parses for: \" is \".*$

  * Metacritic function: checks metacritic score
  * if no reviews: return "N/A"
  * if less than 4 critic reviews: return each
  * if more than 4 critic reviews: return overall
  * return overall user score
  */

//function takes parsed data and puts it into Reddit table format

//display parsed data in non-editable text area underneath "SUBMIT" button
