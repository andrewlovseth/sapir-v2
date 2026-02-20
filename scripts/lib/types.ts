/** Shared types for the article import pipeline */

export interface ArticleFolder {
  /** Folder path on disk */
  path: string;
  /** Folder name (e.g., "02_foer_foster_1") */
  dirName: string;
  /** Sequence number from folder name */
  sequence: number;
  /** Author last names parsed from folder name */
  authorLastNames: string[];
  /** Path to .docx file */
  docxPath: string;
  /** Path to .pdf file */
  pdfPath: string;
  /** Path to dropcap .jpg file */
  dropcapPath: string;
}

export interface ParsedArticle {
  /** HED line (headline) — for matching/verification */
  hed: string;
  /** SUBHED line — becomes ACF dek field */
  dek: string;
  /** The dropcap letter extracted from [X DROP CAP] notation */
  dropcapLetter: string;
  /** Body paragraphs as clean HTML strings (no wrapping tags) */
  paragraphs: string[];
  /** Indices where section separators should be placed (before this paragraph index) */
  separatorIndices: number[];
  /** Whether this is a conversation-format article */
  isConversation: boolean;
  /**
   * Index of the last "body" paragraph (before any sign-off material).
   * This paragraph gets the `last-p` class for the dingbat ornament.
   * Sign-off paragraphs (dates, author names) come after and get right-alignment.
   */
  lastBodyIndex: number;
  /** Indices of sign-off paragraphs (right-aligned, e.g. dates, author names) */
  signoffIndices: number[];
  /** Ranges of paragraphs inside [[block q]] markers → wp:pullquote.basic */
  blockQuoteRanges: { start: number; end: number }[];
}

export interface MatchedArticle {
  folder: ArticleFolder;
  parsed: ParsedArticle;
  /** WordPress post ID */
  postId: number;
  /** WordPress post title */
  postTitle: string;
  /** Whether the post already has content */
  hasContent: boolean;
  /** Whether dropcap is already uploaded */
  hasDrop: boolean;
  /** Whether PDF is already uploaded */
  hasPdf: boolean;
}

export interface ImportResult {
  postId: number;
  postTitle: string;
  success: boolean;
  skipped: boolean;
  error?: string;
  actions: string[];
}

/** ACF field keys for articles */
export const ACF_FIELDS = {
  dek: "field_63bf69d90e5ad",
  display_title: "field_606e256e8e135",
  issue: "field_605cd86033e5b",
  author: "field_605cd85233e5a",
  interviewers: "field_64078dff145fe",
  dropcap: "field_605cd84833e59",
  pdf: "field_60661569538a8",
  pullquote: "field_637509f1624f4",
} as const;
