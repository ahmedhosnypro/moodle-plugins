import Sortable from 'sortablejs';
import {data, formData, registeredFields as rFields} from '../common/data';
import h from '../common/helpers';
import events from '../common/events';
import dom from '../common/dom';
import {uuid, getString, numToPercent} from '../common/utils';

/**
 * Setup Column elements
 */
export default class Column {
  /**
   * Set defaults and/or load existing columns
   * @param  {String} dataID columnId
   * @return {Object} Column config object
   */
  constructor(dataID) {
    const _this = this;

    const columnID = _this.columnID = dataID || uuid();
    _this.columnData = formData.columns.get(columnID);

    const columnDefaults = {
      fields: [],
      id: columnID,
      config: {},
      className: []
    };

    formData.columns.set(columnID, h.merge(columnDefaults, _this.columnData));

    const resizeHandle = {
      tag: 'li',
      className: 'resize-x-handle',
      action: {
        mousedown: _this.resize,
        touchstart: _this.resize
      },
      content: [dom.icon('triangle-down'), dom.icon('triangle-up')]
    };
    const editWindow = {
      tag: 'li',
      className: 'column-edit group-config'
    };

    let column = {
      tag: 'ul',
      className: ['stage-columns', 'empty-columns'],
      dataset: {
        hoverTag: getString('column')
      },
      action: {
        mouseup: (evt) => {
          const column = evt.target.parentElement;
          if (column.resizing) {
            column.resizing = false;
            column.parentElement.classList.remove('resizing-columns');
          }
        }
      },
      id: _this.columnID,
      content: [dom.actionButtons(_this.columnID), editWindow, resizeHandle],
      fType: 'columns'
    };
    column = dom.create(column);
    this.processConfig(column);

    events.columnResized = new CustomEvent('columnResized', {
      detail: {
        column,
        instance: _this
      }
    });

    const sortable = this.sortable = Sortable.create(column, {
      animation: 150,
      fallbackClass: 'field-moving',
      forceFallback: h.isFireFoxEdge(),
      group: {
        name: 'columns',
        pull: true,
        put: ['columns', 'controls'],
        revertClone: true
      },
      sort: true,
      onEnd: _this.onEnd,
      onAdd: _this.onAdd,
      onSort: _this.onSort,
      onRemove: _this.onRemove,
      draggable: '.stage-fields',
      handle: '.item-handle',
    });

    dom.columns.set(columnID, {column, sortable});

    return column;
  }

  /**
   * Column sorted event
   * @param  {Object} evt sort event data
   * @return {Object} Column order, array of column ids
   */
  onSort(evt) {
    return data.saveFieldOrder(evt.target);
  }

  /**
   * Process column configuration data
   * @param  {Object} column
   */
  processConfig(column) {
    const _this = this;
    const columnData = formData.columns.get(_this.columnID);
    if (columnData.config.width) {
      column.dataset.colWidth = columnData.config.width;
      column.style.width = columnData.config.width;
      column.style.float = 'left';
    }
  }

  /**
   * Event when column is added
   * @param  {Object} evt
   */
  onAdd(evt) {
    const {from, item, to} = evt;
    if (!item.classList.contains('layout-tab-control')) {
      const fromColumn = from.fType === 'columns';
      const fromControl = from.fType === 'controlGroup';
      if (fromControl) {
        if (dom.license != 'available') {
          let text = evt.item.firstChild.lastChild.wholeText;
          text = getString('dragndrop', text);
          dom.proWarning(text);
        } else {
          const meta = h.get(rFields[item.id], 'meta');
          if (meta.group !== 'layout') {
            const field = dom.addField(to.id, item.id);
            to.insertBefore(field, to.childNodes[evt.newIndex]);
            data.saveFieldOrder(to);
            data.save('fields', to.id);
          } else {
            if (meta.id === 'layout-column') {
              const row = to.parentElement;
              dom.addColumn(row.id);
              dom.columnWidths(row);
            }
          }
        }
        dom.remove(evt.item);
      }

      dom.fieldOrderClass(to);

      if (fromColumn) {
        dom.fieldOrderClass(from);
      }

      dom.emptyClass(evt.to);

      data.save();
      to.classList.remove('hovering-column');
    } else {
      if (dom.license != 'available') {
        let text = evt.item.firstChild.lastChild.wholeText;
        text = getString('dragndrop', text);
        dom.remove(evt.item);
        dom.proWarning(text);
      } else {
        formData.settings.set('tabSettings', dom.getTabDefaultConfigs()); // Adding empty settings in formData
        dom.createTabContainer(item);
      }
    }
    dom.checkSingle();
  }

  /**
   * Event when field is removed from column
   * @param  {Object} evt
   */
  onRemove(evt) {
    if (evt.from.parent) {
      dom.columnWidths(evt.from.parentElement);
      data.saveColumnOrder(evt.from.parentElement);
    }
    dom.emptyClass(evt.from);
  }

  /**
   * Callback for when dragging ends
   * @param  {Object} evt
   */
  onEnd(evt) {
    const {to, from} = evt;

    if (from.classList.contains('empty-columns')) {
      dom.removeEmpty(evt.from);
      return;
    }

    dom.fieldOrderClass(to);
    dom.fieldOrderClass(from);

    if (from.parent) {
      dom.columnWidths(from.parentElement);
    }

    data.save();

    if (to) {
      to.classList.remove('hovering-column');
    }
  }

  /**
   * Handle column resizing
   * @param  {Object} evt resize event
   */
  resize(evt) {
    const resize = {};
    const column = evt.target.parentElement;
    const sibling = column.nextSibling || column.previousSibling;
    const row = column.parentElement;
    const rowStyle = dom.getStyle(row);
    const rowPadding = parseFloat(rowStyle.paddingLeft) +
    parseFloat(rowStyle.paddingRight);
    let colWidthPercent;
    let sibWidthPercent;

    /**
     * Set the width before resizing so the column
     * does not resize near window edges
     * @param  {Object} evt
     */
    function setWidths(evt) {
      let clientX;
      if (evt.type === 'touchmove') {
        clientX = evt.touches[0].clientX;
      } else {
        clientX = evt.clientX;
      }
      const newColWidth = (resize.colStartWidth + clientX - resize.startX);
      const newSibWidth = (resize.sibStartWidth - clientX + resize.startX);

      const percent = width => (width / resize.rowWidth * 100);
      colWidthPercent = parseFloat(percent(newColWidth));
      sibWidthPercent = parseFloat(percent(newSibWidth));

      column.dataset.colWidth = numToPercent(colWidthPercent.toFixed(1));
      sibling.dataset.colWidth = numToPercent(sibWidthPercent.toFixed(1));

      column.style.width = numToPercent(colWidthPercent);
      sibling.style.width = numToPercent(sibWidthPercent);
    }

    resize.move = evt => {
      setWidths(evt);
      resize.resized = true;
    };

    resize.stop = function() {
      window.removeEventListener('mousemove', resize.move);
      window.removeEventListener('mouseup', resize.stop);
      window.removeEventListener('touchmove', resize.move);
      window.removeEventListener('touchend', resize.stop);
      if (!resize.resized) {
        return;
      }
      const columnData = formData.columns.get(column.id);
      const sibColumnData = formData.columns.get(sibling.id);
      const row = column.parentElement;
      row.querySelector('.column-preset').value = 'custom';
      row.classList.remove('resizing-columns');
      columnData.config.width = column.dataset.colWidth;
      sibColumnData.config.width = sibling.dataset.colWidth;
      resize.resized = false;
      data.save();
    };

    resize.start = (function(evt) {
      if (evt.type === 'touchstart') {
        resize.startX = evt.touches[0].clientX;
      } else {
        resize.startX = evt.clientX;
      }
      row.classList.add('resizing-columns');

      // Remove bootstrap column classes since we are custom sizing
      const reg = /\bcol-\w+-\d+/g;
      column.className.replace(reg, '');
      sibling.className.replace(reg, '');

      // eslint-disable-next-line
      resize.colStartWidth = column.offsetWidth || dom.getStyle(column, 'width');
      // eslint-disable-next-line
      resize.sibStartWidth = sibling.offsetWidth || dom.getStyle(sibling, 'width');
      resize.rowWidth = row.offsetWidth - rowPadding; // Compensate for padding

      window.addEventListener('mouseup', resize.stop, false);
      window.addEventListener('mousemove', resize.move, false);
      window.addEventListener('touchend', resize.stop, false);
      window.addEventListener('touchmove', resize.move, false);
    })(evt);
  }
}
